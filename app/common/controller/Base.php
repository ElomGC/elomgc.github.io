<?php
declare (strict_types = 1);

namespace app\common\controller;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use app\facade\model\{Link, LinkClass, Nav};
use think\exception\{HttpResponseException,ValidateException};
use think\facade\{Cache, Config, View, Session};
use think\{App,Response,Route,Validate};
use worm\NodeFormat;

/**
 * 控制器基础类
 */
abstract class Base{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * @var array 站点信息
     */
    protected $webdb = [];
    /**
     * @var 用户信息
     */
    protected $wormuser = [];
    /**
     * @var 请求参数
     */
    protected $getdata = [];
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app){
        $this->app     = $app;
        $this->request = $this->app->request;
        // 控制器初始化
        $this->initialize();
    }
    // 初始化
    protected function initialize(){
        if(!is_file(base_path().'app.lock') && app('http')->getName() != 'install'){
            $this->redirect(url('/install/index')->build());
        }else if(app('http')->getName() != 'install'){
            if(is_dir(base_path().'install')){
                Common::del_dir(base_path().'install');
            }
            if(is_dir(root_path().'view/install')){
                Common::del_dir(root_path().'view/install');
            }
            $this->webdb = getWebdb();
        }
        if(Session::has('userdb')){
            $this->wormuser = Session::get(Session::has('_admin_') ? '_admin_' : 'userdb');
        }
        //  标准化控制器名称
        $this->request->setController(toUnderScore($this->request->controller()));
        $this->getdata = Common::data_trim($this->request->param());
        unset($this->getdata['0']);
        View::assign(['webdb' => $this->webdb,'getdata' => $this->getdata,'wormuser'=>$this->wormuser]);
    }

    /**
     * @var bool 默认获取数据为页面获取
     */
    protected $index_list = true;
    /**
     * 加载模板文件
     */
    protected $tempview;
    protected function tempview($temp = null){
        $is_mobile = $this->is_mobile();
        $temp_view = $this->app->config->get('view');
        $temp_name = $temp ? $temp : $temp_view['view_default_dir'];
        $temp_view['view_path'] = empty($temp_view['view_default_path']) ? $temp_view['view_path'] : $temp_view['view_default_path'];
        $tpl_replace_string = $temp_view['tpl_replace_string'];
        $tpl_replace_string['__IMAGES__'] = is_dir("{$temp_view['view_path']}/{$temp_name}/images") ? "/{$temp_view['view_path']}/{$temp_name}/images" : "/{$temp_view['view_path']}/default/images";
        $_viewPath = is_dir("{$temp_view['view_path']}/{$temp_name}/template/") ? "{$temp_view['view_path']}/{$temp_name}/template/" : "{$temp_view['view_path']}/default/template/";
        if($is_mobile){
            $_viewPath = is_dir("{$temp_view['view_path']}/default/template/mobile") ? "{$temp_view['view_path']}/default/template/mobile/" : $_viewPath;
            $_viewPath = is_dir("{$temp_view['view_path']}/{$temp_name}/template/mobile") ? "{$temp_view['view_path']}/{$temp_name}/template/mobile/" : $_viewPath;
        }
        $temp_view['view_default_path'] = empty($temp_view['view_default_path']) ? $temp_view['view_path'] : $temp_view['view_default_path'];
        $temp_view['view_path'] = $_viewPath;
        $temp_view['view_open_path'] = $temp_name;
        $temp_view['tpl_replace_string'] = $tpl_replace_string;
        $this->tempview = $temp_view['view_path'];
        Config::set($temp_view, 'view');
        View::assign(['head' => 'head','foot' => 'foot']);
    }
    protected function hasview($file = ''){
        $is_mobile = $this->is_mobile();
        $temp_view = $this->app->config->get('view');
        if(!is_file("{$temp_view['view_path']}{$file}")){
            $this->tempview('default');
            $temp_view = $this->app->config->get('view');
        }
        return "{$temp_view['view_path']}{$file}";
    }
    //  定义筛选条件
    protected $map = array();
    protected function getMap(){
        return $this->getdata;
    }
    //  定义排序方式
    protected function getOrder(){
        return 'id desc';
    }
    //  认证token
    protected function res_token($user,$curtime = null){
        $curtime = empty($curtime) ? time() : $curtime;
        $cheksum['token'] = utf8_encode(substr(SHA1($user['u_groupid'].$user['uid'].$user['u_name'].$curtime),10,8));
        $cheksum['times'] = $curtime;
        return $cheksum;
    }
    //  验证token
    protected function res_returntoken($data,$token):bool
    {
        $oldtoken = $this->res_token($data,$data['wrtimes']);
        if($token == $oldtoken['token']){
            return true;
        }
        return false;
    }
    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
    /**
     * @param string $msg  错误提示
     * @param string|null $url  跳转链接
     * @param string $data  返回数据
     * @param int $wait 等待时间
     * @param array $header 返回头
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = []){
        if (is_null($url)) {
            $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Route::buildUrl($url);
        }
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        $type = $this->getResponseType();
        if(strtolower($type) == 'html'){
            $type = 'view';
            $response = Response::create(config('app.dispatch_success_tmpl'), $type)->assign($result)->header($header);
        }else{
            $response = Response::create($result, $type)->header($header);
        }
        throw new HttpResponseException($response);
    }
    //  检测浏览器属性
    protected function is_weixin(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'miniprogram') !== false) {
            return 'wx-mp';
        }else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return 'wx';
        }
        return 'h5';
    }
    //  检测是否为手机
    protected function is_mobile() {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
            return true;
        if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
            return true;
        if (isset ($_SERVER['HTTP_VIA']))
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param string $msg   提示信息
     * @param string|null $url  跳转链接
     * @param string $data  返回数据
     * @param int $wait 等待时间
     * @param array $header 返回头
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = []){
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Route::buildUrl($url);
        }
        $result = [
            'code' => 1,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];
        $type = $this->getResponseType();
        if(strtolower($type) == 'html'){
            $type = 'view';
            $response = Response::create(config('app.dispatch_success_tmpl'), $type)->assign($result)->header($header);
        }else{
            $response = Response::create($result, $type)->header($header);
        }
        throw new HttpResponseException($response);
    }
    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param  mixed $data 要返回的数据
     * @param  integer $code 返回的code
     * @param  mixed $msg 提示信息
     * @param  string $type 返回数据格式
     * @param  array $header 发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = 'json', array $header = []){
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }
    /**
     * URL重定向
     * @access protected
     * @param  string $url 跳转的URL表达式
     * @param  integer $code http code
     * @param  array $with 隐式传参
     * @return void
     */
    protected function redirect($url, $code = 302, $with = []){
        $response = Response::create($url, 'redirect');

        $response->code($code)->with($with);

        throw new HttpResponseException($response);
    }
    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(){
        return $this->request->isJson() || $this->request->isAjax() ? 'json' : 'html';
    }
    //  获取导航
    protected function getNav():array {
        if(Cache::has('NavList')){
            return Cache::get('NavList');
        }
        $navlist = Nav::getlist();
        $_class = array_unique(array_column($navlist,'class'));
        $_navlist = [];
        foreach ($_class as $k => $v){
            $_v = Common::del_file($navlist,'class',$v);
            $_navlist[$v] = NodeFormat::toLayer($_v);
        }
        Cache::set('NavList',$_navlist);
        return $_navlist;
    }
    //  获取友情链接
    protected function getLink():array
    {
        if(Cache::has('LinkList')){
            $classList = Cache::get('LinkList');
        }else {
            $_classlist = LinkClass::getList();
            $linkList = Link::getList(['class' => array_column($_classlist, 'id')]);
            $classList = [];
            foreach ($_classlist as $k => $v) {
                $classList[$v['id']] = [
                    'title' => $v['title'],
                    'data' => Common::del_file($linkList, 'class', $v['id']),
                ];
            }
            Cache::set('LinkList', $classList);
        }
        $classList = Upload::editadd($classList,false);
        return $classList;
    }
    protected function isLogin():bool {
        if(!empty($this->wormuser)){
            return true;
        }
        return false;
    }
}
