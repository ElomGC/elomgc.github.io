<?php
declare(strict_types = 1);
namespace app\common\controller\member;

use app\common\controller\MemberBase;
use app\common\model\UserApi;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;
use think\facade\View;
use weixin\WxBase;
use worm\NodeFormat;

abstract class Article extends MemberBase
{
    use AddEditList;
    protected $basename;
    protected $fileModel;
    protected $baseModel;
    protected $baseModelList;
    protected $partModel;
    protected $partModelList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\".$this->basename."\\Article";
        $this->model = new $mdodel;
        $fileModel = "app\\common\\model\\".$this->basename."\Artmodelfile";
        $this->fileModel = new $fileModel;
        $baseModel = "app\\common\\model\\".$this->basename."\Artmodel";
        $this->baseModel = new $baseModel;
        $partModel = "app\\common\\model\\".$this->basename."\Part";
        $this->partModel = new $partModel;
        $this->validate = "app\\common\\validate\\ArtArticle";
        $this->getConf();
    }
    protected function getConf()
    {
        $this->baseModelList = $this->baseModel->getList(['status' => '1']);
        if (empty($this->baseModelList)) {
            $this->error("模型不存在", url("Index/index")->build());
        }
        $mid = array_column($this->baseModelList,'id');
        $this->partModelList = NodeFormat::toList($this->partModel->getList(['mid' => $mid,'group_edit' => $this->wormuser['u_groupid']]));
        if(empty($this->partModelList)){
            $this->error("栏目不存在，请联系管理员",url("Index/index")->build());
        }
        $this->list_base['title'] = "内容";
        //  如果携带了栏目ID，就只展示本栏目的内容
        $getfu = [];
        if(!empty($this->getdata['fid'])){
            $getfu['fid'] = $this->getdata['fid'];
            $_part = Common::del_file($this->partModelList,'id',$this->getdata['fid']);
            if(empty($_part)){
                $this->partModelList = $this->partModel->getList(['id' => $this->getdata['fid'],'getdeltime' => '1001','status' => 'a']);
            }
        }
        if(!empty($this->getdata['mid'])){
            $getfu['mid'] = $this->getdata['mid'];
            $_model = Common::del_file($this->baseModelList,'id',$this->getdata['mid']);
            if(empty($_model)){
                $this->baseModelList = $this->baseModel->getList(['id' => $this->getdata['mid'],'getdeltime' => '1001','status' => 'a']);
                $this->partModelList = $this->partModel->getList(['mid' => $this->getdata['mid'],'getdeltime' => '1001','status' => 'a']);
                $this->list_rightbtn = [];
            }
        }
        $this->list_base['add'] = url('create')->build()."?".http_build_query($getfu);
        $_partlist = [];
        foreach ($this->partModelList as $k => $v){
            $_partlist[$v['id']] = $v['title'];
        }
        $this->list_file = [
            ['file' => 'fid','title' => '分类','type' => 'radio','radio' => $_partlist,'class' => 'cx-text-center','width' => '100'],
            ['file' => 'title','title' => '标题','type' => 'link','target' => '1','uri' => url("/{$this->basename}/article-__id__")->domain(true)->build()],
            ['file' => 'addtime','title' => '发表时间','type' => 'text','class' => 'cx-text-center','width' => '110',],
            ['file' => 'status','title' => '状态','type' => 'radio','class' => 'cx-text-center','width' => '80','radio' => ['0'=> "<i class='cx-icon cx-icontiaokuanshengming cx-text-yellow'></i>",'1'=>"<i class='cx-icon cx-iconroundcheck cx-text-green'></i>",'2'=>"<i class='cx-icon cx-iconclose cx-text-red'></i>"]]
        ];
        if(in_array($this->request->action(),['create','edit'])){
            $this->list_base['uri'] = url('save')->build();
            $this->getdata['mid'] = empty($this->getdata['mid']) ? null : $this->getdata['mid'];
            if(!empty($this->getdata['id'])){
                $_article = $this->model->whereId($this->getdata['id'])->whereUid($this->wormuser['uid'])->find();
                if(empty($_article) || $_article['uid'] != $this->wormuser['uid']){
                    $this->error("非法访问");
                }
                $this->getdata = $this->model->getOne($_article['mid'],$_article['id'],$this->wormuser['uid']);
            }else if(empty($this->getdata['mid']) && !empty($this->getdata['fid'])){
                $part = Common::del_file($this->partModelList,'id',$this->getdata['fid']);
                $this->getdata['mid'] = $part['0']['mid'];
            }
            $this->form_list = $this->getBaseForm($this->getdata['mid']);
        }
    }
    protected function getMap(){
        $mid = empty($this->getdata['mid']) ? array_column($this->baseModelList,'id') : $this->getdata['mid'];
        $fid = empty($this->getdata['fid']) ? array_column($this->partModelList,'id') : $this->getdata['fid'];
        if(!empty($this->getdata['fid'])){
            $_part = Common::del_file($this->partModelList,'id',$fid);
            $mid = $_part['0']['mid'];
        }
        $map = [
            'mid' => $mid,
            'fid' => $fid,
            'status' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
            'uid' => $this->wormuser['uid'],
            'page' => empty($this->getdata['page']) ? '' : $this->getdata['page'],
            'limit' => empty($this->getdata['limit']) ? '' : $this->getdata['limit'],
            'filed' => empty($this->getdata['filed']) ? '' : $this->getdata['filed'],
            'key' => empty($this->getdata['key']) ? '' : $this->getdata['key'],
        ];
        return $map;
    }
    //  获取模型
    protected function getBaseForm($mid){
        $file_list = $this->fileModel->getList(['mid' => $mid]);
        $model = Common::del_file($this->baseModelList,'id',$mid);
        $model = $model['0'];
        //  获取栏目列表
        $partlist = Common::del_file($this->partModelList,'mid',$mid);
        if(empty($partlist)){
            $this->error("请创建栏目");
        }
        $_partlist = [];
        foreach ($partlist as $k => $v){
            $_partlist[$v['id']] = $v['title_display'];
        }
        $res[] = ['file' => 'fid','title' => '发布到','title_edit' => '请选择...','type' => 'select','data' => ['list' => $_partlist,'default' => ''],'required' => true,'required_list' => 'number'];
        $res[] = ['file' => 'mid','title' => '审核','type' => 'text','type_edit' => 'hidden','required' => true,'default' => $mid,'required_list' => 'number',];
        $res[] = ['file' => 'id','title' => '审核','type' => 'text','type_edit' => 'hidden'];
        $res[] = ['file' => 'uid','title' => '审核','type' => 'text','type_edit' => 'hidden'];
        foreach ($file_list as $k => $v){
            if($v['form_type'] == 'rescont'){
                continue;
            }
            $res[] = Common::ReadFile($v);
        }
        if($model['order'] == '1'){
            $res[] = ['file' => 'money','title' => '市场价格','type' => 'text','type_group' => '订单结算','default' => '0','type_unit' => '元','required' => true,'required_list' => 'number'];
            $res[] = ['file' => 'money_zk','title' => '市场折扣价格','type' => 'text','type_group' => '订单结算','default' => '0','tip' => '为0时不打折','type_unit' => '元','required' => true,'required_list' => 'number'];
            if($model['order_group'] == '1' || $model['order_group'] == '2'){
                $usermodel = new \app\common\model\User();
                $usergroup = $usermodel->getAuthGroup();
                $usergroup = Common::del_file($usergroup,'group_type','1');
                $usergroup = array_merge([],$usergroup);
                $moneyText = $model['order_group'] == '1' ? "价格" : "返利";
                foreach ($usergroup as $k => $v){
                    $res[] = ['file' => "group{$v['id']}",'title' => "{$v['title']}{$moneyText}",'type' => 'text','type_group' => '订单结算','default' => '0','type_unit' => '元','required' => true,'required_list' => 'number'];
                }
            }
        }
        $see_list = ['0' => "草稿",'1' => '审核'];
        if(!empty($model['see_add'])){
            $see_list['2'] = "拒绝";
        }
        if(!empty($model['see_picurl'])){
            $res[] = ['file' => 'picurl','title' => '缩略图','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'];
        }
        if(!empty($model['see_keyword'])){
            $res[] = ['file' => 'text','title' => '标签','type' => 'text','tip' => '多个标签请用英文“,”进行分割'];
        }
        if(!empty($model['see_description'])){
            $res[] = ['file' => 'description','title' => '摘要','type' => 'textarea'];
        }
        return $res;
    }
    public function save()
    {
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if($this->request->isPut() && empty($data['id'])){
            $this->error("非法访问");
        }
        $data['uid'] = empty($data['uid']) ? $this->wormuser['uid'] : $data['uid'];
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $form_list = $this->getBaseForm($data['mid']);
        $_data = empty($data['id']) ? [] : $this->model->getOne($data['mid'],$data['id']);
        $data = Common::SetReadFile($form_list,$data,$_data);
        if($data['code'] == '0'){
            $this->error($data['msg']);
        }
        $data = array_merge($_data,$data['data']);
        $data =$this->model->getEditAdd($data);
        $res_title = !empty($this->getdata['id']) ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            if($this->basename == 'cms'){
                $data = array_merge($add,$data);
                $this->sendsms($data,'cms');
            }
            $this->success("{$res_title}成功",url("{$this->basename}.article/index")->build());
        }
        $this->error("{$res_title}失败");
    }
    protected function sendsms($data,$model = 'forms')
    {
        $add = [];
        if($model == 'cms') {
            if ($data['mid'] == '7') {
                if (empty($this->webdb['wx_user_openid'])) {
                    return true;
                }
                $add = [
                    'touser' => $this->webdb['wx_user_openid'],
                    'template_id' => 'hg8FzCwjhvaylmbdfweHa0yZzr-pIZcO6_1-5XojjAc',
                    'data' => [
                        'first' => [
                            'value' => "一条新上报的民情消息！",
                        ],
                        'keyword1' => [
                            'value' => $data['title'],
                        ],
                        'keyword2' => [
                            'value' => $data['address'],
                        ],
                        'remark' => [
                            'value' => "请及时处理",
                        ],
                    ],
                ];
                $uModel = new UserApi();
                $user = $uModel->whereUid($data['uid'])->whereType('wx')->whereSubscribe('1')->find();
                if(!empty($user)){
                    $_add = [
                        'touser' => $user['openid'],
                        'template_id' => 'WtO21lQA10BGHrITDTylnS9JFMn-DYKptVIA702yut8',
                        'url' => $this->webdb['web_url'] . '/member.html',
                        'data' => [
                            'first' => [
                                'value' => "您好！您上报到和美罗庄的事件已被受理！",
                            ],
                            'keyword1' => [
                                'value' => $data['id'],
                            ],
                            'keyword2' => [
                                'value' => $data['title'],
                            ],
                            'keyword3' => [
                                'value' => is_int($data['addtime']) ? date('Y-m-d',$data['addtime']) : $data['addtime'],
                            ],
                            'keyword4' => [
                                'value' => Db::name('user')->whereUid($data['uid'])->value('u_uname'),
                            ],
                            'remark' => [
                                'value' => "我们将尽快处理，请耐心等耐",
                            ],
                        ],
                    ];
                    $wxBase = new WxBase();
                    $token = $wxBase->get_base_token($this->webdb['wx_appid'], $this->webdb['wx_appsecret']);
                    $wxBase->sendMessage($token, $_add);
                }
            }
        }
        if(!empty($add)) {
            $wxBase = new WxBase();
            $token = $wxBase->get_base_token($this->webdb['wx_appid'], $this->webdb['wx_appsecret']);
            $wxBase->sendMessage($token, $add);
        }
    }
    public function edit()
    {
        if(empty($this->getdata['id'])){
            $this->error("非法访问");
        }
        $res = $this->model->whereId($this->getdata['id'])->find()->toArray();
        $res = $this->model->getOne($res['mid'], $res['id']);
        $_fileList = $this->getBaseForm($res['mid']);
        View::assign(['file_list' => $_fileList]);
        return $this->viewMemberAdd($res);
    }

    public function read($id){
        $data = $this->model->whereId($id)->whereUid($this->wormuser['uid'])->find();
        if(empty($data)){
            $this->error('内容不存在');
        }
        $data = $this->model->getOne($data['mid'],$data['id'],empty($this->wormuser['uid']) ? '' : $this->wormuser['uid']);
        $file_list = $this->fileModel->getList(['mid' => $data['mid']]);
        foreach ($file_list as $k => $v){
            $_fileList[$k] = Common::ReadFile($v);
        }
        View::assign(['file_list' => $_fileList]);
        return $this->viewMemberRead($data);
    }
    //  内容聚合
    public function household(){
        $map = $this->getMap();
        unset($map['uid']);
        $getlist = $this->model->getHousehole($map);
        return $this->viewApiList($getlist);
    }
}