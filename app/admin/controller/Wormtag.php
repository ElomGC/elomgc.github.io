<?php
declare(strict_types = 1);

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\exception\ValidateException;
use think\facade\View;
use worm\NodeFormat;

class Wormtag extends AdminBase
{
    use AddEditList;
    protected $Read;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\WormTag";
        $this->validate = "app\\common\\validate\\WormTag";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        if(!empty($this->getdata['id'])){
            $this->Read = $this->model->getOne($this->getdata['id']);
            $this->Read['conf'] = empty($this->Read['conf']) ? [] : json_decode($this->Read['conf'],true);
        }
    }
    /**
     * 标签入口
     * @return mixed
     */
    public function homeedit(){
        if(empty($this->getdata['id'])){
            $setdata = explode('_',$this->getdata['name']);
            $setdata = [
                'model' => $setdata['0'],
                'contr' => $setdata['1'],
                'action' => $setdata['2'],
                'type' => $setdata['3'],
                'TagModel' => $setdata['4'],
                'TagName' => $setdata['5'],
                'tempname' => $this->getdata['t'],
            ];
            $this->Read = $this->model->setOne($setdata);
        }
        $_r = (string) $this->Read['type'];
        return $this->$_r($this->Read['id']);
    }
    /**
     * 修改显示内容
     * @param $id
     * @return \think\response\View
     */
    public function articlelist($id){
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            return $this->save($data);
        }
        $this->list_temp = 'articlelist';
        //  读取模板
        $filelist = $this->getfilelist(empty($this->Read['temptype']) ? 'label/articlelist/list' : 'label/articlelist/one');
        $modellist = $this->getmodellist($this->Read['TagModel']);
        $partlist = $this->getpartlist(['m' => $this->Read['TagModel'],'class' => '0','mid' => $this->Read['TagMid']]);

        View::assign(['filelist' => $filelist,'modellist' => $modellist,'partlist' => $partlist,]);
        return $this->viewAdminAdd($this->Read);
    }
    /**
     * 修改调用栏目
     * @param $id
     * @return \think\response\View
     */
    public function partlist($id){
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            return $this->save($data);
        }
        $this->list_temp = 'partlist';
        //  读取模板
        $filelist = $this->getfilelist(empty($this->Read['temptype']) ? 'label/partlist/list' : 'label/partlist/one');
        $modellist = $this->getmodellist($this->Read['TagModel']);
        $partlist = $this->getpartlist(['m' => $this->Read['TagModel'],'mid' => $this->Read['TagMid'],'class' => 'a']);
        View::assign(['filelist' => $filelist,'modellist' => $modellist,'partlist' => $partlist,]);
        return $this->viewAdminAdd($this->Read);
    }
    /**
     * 修改调用栏目
     */
    public function navlist($id){
        if($this->request->isPost()) {
            $data = Common::data_trim(input('post.'));
            $data['TagMid'] = empty($data['TagMid']) ? [] : explode(',',$data['TagMid']);
            $data['template'] = Common::data_html($data['template']);
            return $this->save($data);
        }
        $this->list_temp = 'navlist';
        $model = new \app\common\model\NavClass();
        $modellist =$model->getList();
        $partlist = $this->getNavlist(['class' => empty($this->Read['TagMid']) ? $modellist['0']['id'] : $this->Read['TagMid']]);
        $filelist = $this->getfilelist(empty($this->Read['temptype']) ? 'label/navlist/list' : 'label/navlist/one');
        View::assign(['filelist' => $filelist,'modellist' => $modellist,'partlist' => $partlist]);
        return $this->viewAdminAdd($this->Read);
    }
    public function htmlcont($id){
        if($this->request->isPost()) {
            $data = Common::data_trim(input('post.'));
            $_img = [
                'oldimg' => empty($this->Read['template']) ? null : $this->Read['template'],
                'newimg' => $data['template'],
            ];
            $data['template'] = Upload::setEditOr($_img,'tag/'.date('Y-m', time()));
            $data['template'] = Upload::editadd($data['template']);
            return $this->save($data);
        }
        $this->list_temp = 'htmlcont';
        return $this->viewAdminAdd($this->Read);
    }

    public function advertising($id){
        if($this->request->isPost()) {
            $data = Common::data_trim(input('post.'));
            $data['TagMid'] = empty($data['TagMid']) ? [] : explode(',',$data['TagMid']);
            $data['TagFid'] = empty($data['TagFid']) ? [] : explode(',',$data['TagFid']);
            $data['template'] = Common::data_html($data['template']);
            return $this->save($data);
        }
        $this->list_temp = 'advertising';
        $model = new \app\common\model\Advertising();
        $this->Read['TagMid'] = empty($this->Read['TagMid']) ? '1' : $this->Read['TagMid'];
        $partlist = $model->getList(['class' => $this->Read['TagMid'],'limit' => '1000']);
        if($this->Read['TagFid']){
            $this->Read['ad'] = $model->getOne($this->Read['TagFid']);
        }
        $_temp = 'label/advertising/pic';
        $_temp = $this->Read['TagMid'] == '2' ? 'label/advertising/piclist' : $_temp;
        $_temp = $this->Read['TagMid'] == '3' ? 'label/advertising/pictext' : $_temp;
        $_temp = $this->Read['TagMid'] == '4' ? 'label/advertising/text' : $_temp;
        $filelist = $this->getfilelist($_temp);
        View::assign(['partlist' => $partlist['data'],'filelist' => $filelist,]);
        return $this->viewAdminAdd($this->Read);
    }
    /**
 * 获取导航列表
 * @param $data
 * @return mixed
 */
    protected function getNavlist($data){
        $model = new \app\common\model\Nav();
        $partlist = $model->getHomeList($data);
        foreach ($partlist as $k => $v){
            $partlist[$k] = [
                'id' => $v['id'],
                'title' => $v['title_display'],
            ];
        }
        return $partlist;
    }
    /**
     * 获取栏目列表
     * @param $data
     * @return mixed
     */
    protected function getpartlist($data){
        $model = "app\\common\\model\\{$data['m']}\\Part";
        $model = new $model;
        $partlist = $model->getPartList(['class' => isset($data['class']) ? $data['class'] : '','mid' => empty($data['mid']) ? '' : $data['mid']]);
        foreach ($partlist as $k => $v){
            $partlist[$k] = [
                'id' => $v['id'],
                'title' => $v['title_display'],
            ];
        }
        return $partlist;
    }
    /**
     * @param $modelname 获取模型
     * @return mixed
     */
    protected function getmodellist($modelname){
        $model = "app\\common\\model\\{$modelname}\\Artmodel";
        $model = new $model;
        $modellist = $model->getList();
        foreach ($modellist as $k => $v){
            $modellist[$k] = [
                'id' => $v['id'],
                'title' => empty($v['futitle']) ? $v['title'] : $v['futitle'],
            ];
        }
        return $modellist;
    }
    /**
     * @param $path 查询模板目录文件
     * @return array
     */
    protected function getfilelist($path){
        $filelist = get_dir_file(public_path($path),'htm',false);
        asort($filelist);
        foreach ($filelist as $k => $v){
            $v = basename($v);
            $v = explode('.',$v);
            $filelist[$k] = $v['0'];
        }
        return $filelist;
    }
    protected function save($data){
        try {
            validate($this->validate)->scene('add')->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $data['TagMid'] = empty($data['TagMid']) ? '' : implode(',',$data['TagMid']);
        $data['TagFid'] = empty($data['TagFid']) ? '' : implode(',',$data['TagFid']);
        $data = $this->model->getEditAdd($data);
        if($this->model->setOne($data)){
            $this->success("修改成功");
        }
        $this->error("修改失败");
    }
    /**
     * 获取模板代码
     */
    public function getcode(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['n']) || !isset($data['f'])){
            $this->error("非法访问");
        }
        if(!is_file(public_path("label/{$data['n']}").$data['f'].'.htm')){
            $this->error("模板不存在");
        }
        $res = [
            'img' => "/public/label/{$data['n']}/{$data['f']}.jpg",
            'code' => read_file(public_path("label/{$data['n']}").$data['f'].'.htm'),
        ];
        $this->result($res,'1','获取成功');
    }
    /**
     * 获取模型对应字段
     */
    public function getfile(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['m'])){
            $this->error("非法访问");
        }
        $model = "app\\common\\model\\{$data['m']}\\Artmodelfile";
        $model = new $model;
        $filelist = $model->getList(['mid' => empty($data['n']) ? '' : $data['n'],'getField'=> ['sql_file','mid','form_title']]);
        $data['n'] = empty($data['n']) ? array_unique(array_column($filelist,'mid')) : $data['n'];
        $_filelist = array_column($filelist,'sql_file');
        $_count = array_count_values($_filelist);
        $_filelist = array_unique($_filelist);
        foreach ($_filelist as $k => $v){
            if($_count[$v] < count($data['n'])){
                unset($_filelist[$k]);
                continue;
            }
            $_v = Common::del_file($filelist,'sql_file',$v);
            $_v = implode('/',array_unique(array_column($_v,'form_title')));
            $_filelist[$k] = [
                'title' => $_v,
                'filed' => $v,
            ];
        }
        $_filelist = array_merge([],$_filelist);
        array_push($_filelist,['title' => '访问地址','filed' => 'uri',],['title' => '栏目名称','filed' => 'part_name',],['title' => '栏目ID','filed' => 'fid',],['title' => '缩略图','filed' => 'picurl',],['title' => '简介','filed' => 'description',],['title' => '点击量','filed' => 'hist',],['title' => '用户名','filed' => 'u_uname',],['title' => '用户昵称','filed' => 'u_uniname',],['title' => '用户头像','filed' => 'u_icon',],['title' => '发表时间(Y-m-d H:i:s)','filed' => 'addtime',],['title' => '发表日期（Y-m-d）','filed' => 'time_date',],['title' => '发表时间（H:i:s）','filed' => 'time_his',],['title' => '发表日期（年）','filed' => 'time_Y',],['title' => '发表日期（月）','filed' => 'time_m',],['title' => '发表日期（日）','filed' => 'time_d',],['title' => '发表时间（H）','filed' => 'time_H',],['title' => '发表时间（i）','filed' => 'time_i',],['title' => '发表时间（s）','filed' => 'time_s',]);
        $this->result($_filelist,'1','查询成功');
    }
    /**
     * 获取栏目
     */
    public function getpart(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['m'])){
            $this->error("非法访问");
        }
        $partlist = $this->getpartlist(['m' => $data['m'],'class' => $data['c'],'mid' => empty($data['n']) ? '' : $data['n']]);
        $this->result($partlist,'1','查询成功');
    }
    /**
     * 获取导航
     */
    public function getnavs(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['c'])){
            $this->error("非法访问");
        }
        $partlist = $this->getNavlist(['class' => $data['c']]);
        $this->result($partlist,'1','查询成功');
    }
    /** 获取模板 **/
    public function gettemplist(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['d'])){
            $this->error("非法访问");
        }
        $getlist = $this->getfilelist($data['d']);
        $this->result($getlist,'1','查询成功');
    }
}