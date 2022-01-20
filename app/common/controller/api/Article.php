<?php
declare(strict_types = 1);
namespace app\common\controller\api;

use app\common\controller\ApiBase;
use app\common\model\Chinacode;
use app\common\model\Order;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\Db;
use worm\NodeFormat;

abstract class Article extends ApiBase {

    use AddEditList;
    protected $basename;
    protected $fileModel;
    protected $baseModel;
    protected $baseModelList;
    protected $partModel;
    protected $form_list;
    protected $partModelList;
    protected function initialize(){
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\".$this->basename."\\".ucfirst($array['0']['4']);
        $this->model = new $mdodel;
        $fileModel = "app\\common\\model\\".$this->basename."\Artmodelfile";
        $this->fileModel = new $fileModel;
        $baseModel = "app\\common\\model\\".$this->basename."\Artmodel";
        $this->baseModel = new $baseModel;
        $partModel = "app\\common\\model\\".$this->basename."\Part";
        $this->partModel = new $partModel;
        $this->validate = "app\\common\\validate\\ArtArticle";
        $this->baseModelList = $this->baseModel->getList(['status' => '1']);
        $mid = array_column($this->baseModelList,'id');
        $this->partModelList = NodeFormat::toList($this->partModel->getList(['mid' => $mid]));
        $this->getConf();
    }
    protected function getConf(){
        if(in_array($this->request->action(),['create','edit','save'])){
            if(!$this->isLogin()){
                $this->result('','10000','请登录');
            }
            $this->getdata['mid'] = empty($this->getdata['mid']) ? null : $this->getdata['mid'];
            if(!empty($this->getdata['id'])){
                $_old = $this->model->whereId($this->getdata['id'])->find()->toArray();
                if(empty($_old) || $_old['del_time'] > '0'){
                    $this->result('','0','内容不存在');
                }
                $this->getdata = $this->form_list['filedata'] = $this->model->getOne($_old['mid'],$_old['id']);
            }else if(empty($this->getdata['mid']) && !empty($this->getdata['fid'])){
                $part = Common::del_file($this->partModelList,'id',$this->getdata['fid']);
                $this->getdata['mid'] = $part['0']['mid'];
            }
            if(empty($this->getdata['mid'])){
                $this->result('','0','请选择发表内容');
            }
            $this->form_list['filelist'] = $this->getBaseForm($this->getdata);
        }
    }
    protected function getMap(){
        $mid = empty($this->getdata['mid']) ? array_column($this->baseModelList,'id') : $this->getdata['mid'];
        $fid = empty($this->getdata['fid']) ? array_column($this->partModelList,'id') : $this->getdata['fid'];
        if(!empty($this->getdata['fid'])){
            $_part = Common::del_file($this->partModelList,'id',$fid);
            $mid = $_part['0']['mid'];
            if(count(explode(',',$fid)) > 1){
                $_fid = explode(',',$fid);
                $_fid = Common::del_null($_fid);
                foreach ($_fid as $k => $v){
                    $_v = NodeFormat::getChildsId($this->partModelList,$v);
                    $_fid = array_merge($_fid,$_v);
                }
                $fid = array_unique($_fid);
            } else {
                $fid = NodeFormat::getChildsId($this->partModelList,$this->getdata['fid']);
                array_push($fid,$this->getdata['fid']);
            }
        }
        $map = [
            'mid' => $mid,
            'fid' => $fid,
            'status' => !isset($this->getdata['status']) ? '1' : $this->getdata['status'],
            'sid' => empty($this->getdata['sid']) ? '' : $this->getdata['sid'],
            'id' => empty($this->getdata['id']) ? '' : $this->getdata['id'],
            'uid' => empty($this->getdata['uid']) ? null : $this->getdata['uid'],
            'page' => empty($this->getdata['page']) ? '' : $this->getdata['page'],
            'limit' => empty($this->getdata['limit']) ? '' : $this->getdata['limit'],
            'filed' => empty($this->getdata['filed']) ? '' : $this->getdata['filed'],
            'key' => empty($this->getdata['key']) ? '' : $this->getdata['key'],
            'order' => empty($this->getdata['order']) ? '' : $this->getdata['order'],
        ];
//        if(!empty($map['filed'])){
//            $map[$map['filed']] = $this->getdata[$map['filed']];
//        }
        return $map;
    }
    //  获取列表
    public function index()
    {
        $getlist = $this->model->getList($this->getMap());
        if($getlist['total'] > '0'){
            $file_list = array_unique(array_column($getlist['data'],'mid'));
            $file_list = $this->fileModel->getList(['mid' => $file_list]);
            $_resfile = [];
            foreach ($file_list as $k => $v){
                $_v = [
                    'file' => $v['sql_file'],
                    'title' => $v['form_title'],
                ];
                $_resfile[$v['mid']][] = $_v;
            }
            foreach ($getlist['data'] as $k => $v){
                if(empty($getlist['filelist'][$v['mid']])){
                    $getlist['filelist'][$v['mid']] = $_resfile[$v['mid']];
                }
            }
            if(!empty($this->getdata['mid']) && !empty($this->getdata['filed']) && !empty($this->getdata['key'])){
                $_aid = Db::name("{$this->basename}_content_{$this->getdata['mid']}")->whereIn($this->getdata['filed'],$this->getdata['key'])->order('id asc')->value('id');
                $_alllist = Db::name("{$this->basename}_content_{$this->getdata['mid']}")->whereIn($this->getdata['filed'],$this->getdata['key'])->field("{$this->getdata['filed']},id,hist,comment_num")->whereStatus('1')->select()->toArray();
                $getlist['hist'] = array_sum(array_column($_alllist,'hist'));
                $getlist['comment_num'] = array_sum(array_column($_alllist,'comment_num'));
                if(!empty($_aid)){
                    $getlist['article'] = $this->model->getOne($this->getdata['mid'],$_aid);
                }
            }
        }
        return $this->viewApiList($getlist);
    }

    public function read($id){
        $data = $this->model->whereId($id)->find();
        $data = $this->model->getOne($data['mid'],$data['id'],empty($this->wormuser['uid']) ? '' : $this->wormuser['uid']);
        if(empty($data) || $data['status'] != '1' || $data['del_time'] > '0'){
            $this->result('','0','内容不存在');
        }
        if(!empty($data['_order_group'])){
            if($data['_order_group'] == '1'){
                $data = Common::countMoney($data,'groupid',empty($this->wormuser['u_groupid']) ? '0' : $this->wormuser['u_groupid']);
                if(!empty($data['parametermoney'])){
                    foreach ($data['parametermoney'] as $k => $v){
                        $data['parametermoney'][$k] = Common::countMoney($v,'groupid',empty($this->wormuser['u_groupid']) ? '0' : $this->wormuser['u_groupid']);
                    }
                }
            } else if ($data['_order_group'] == '4'){
                $data = Common::countMoney($data,'chinacode',empty($this->getdata['chinacode']) ? '0' : $this->getdata['chinacode']);
                if(!empty($data['parametermoney'])){
                    foreach ($data['parametermoney'] as $k => $v){
                        $data['parametermoney'][$k] = Common::countMoney($v,'chinacode',empty($this->getdata['chinacode']) ? '0' : $this->getdata['chinacode']);
                    }
                }
            }
        }
        Db::name("{$this->basename}_content_{$data['mid']}")->whereId($data['id'])->update(['hist' => $data['hist'] + 1]);
        return $this->viewApiRead($data);
    }
    //  添加内容
    public function create(){
        if(!$this->isLogin()){
            $this->result('','10000','请登录');
        }
        return $this->viewApiRead($this->form_list);
    }
    //  获取模型
    protected function getBaseForm($data){
        $file_list = $this->fileModel->getList(['mid' => $data['mid']]);
        $model = Common::del_file($this->baseModelList,'id',$data['mid']);
        $model = $model['0'];
        $model['edit_group'] = !empty($model['edit_group']) ? explode(',',$model['edit_group']) : [];
        if($model['see_add'] != '1'){
            $this->result('','0','此分类禁止发表内容');
        }
        if(!empty($model['edit_group']) && $this->wormuser['u_groupid'] != '1' && !in_array($this->wormuser['u_groupid'],$model['edit_group'])){
            $this->result('','0','没有此权限');
        }
        $partlist = Common::del_file($this->partModelList,'mid',$data['mid']);
        if(empty($partlist)){
            $this->result('','0','还没有栏目哦，请联系管理员');
        }
        $_partlist = [];
        foreach ($partlist as $k => $v){
            $_partlist[$v['id']] = [
                'fid' => $v['id'],
                'title' => $v['title'],
                'title_display' => $v['title_display'],
            ];
        }
        if(!empty($this->getdata['lock']) && !empty($this->getdata['fid']) && $this->getdata['lock'] == 'fid'){
            $_partlist_ = $_partlist[$this->getdata['fid']];
            $_partlist = [];
            $_partlist[$this->getdata['fid']] = $_partlist_;
        }

        $res = [
            ['file' => 'fid','title' => '发布到','title_edit' => '请选择','type' => 'select','data' => ['list' => $_partlist,'default' => empty($data['fid']) ? '' : $data['fid']],'required' => true,'required_list' => 'number'],
            ['file' => 'id','title' => '内容ID','type' => 'text','type_edit' => 'hidden','default' => empty($data['id']) ? '' : $data['id'],'required' => empty($data['id']) ? '' : true,'required_list' => 'number'],
            ['file' => 'uid','title' => '用户UID','type' => 'text','type_edit' => 'hidden','default' => empty($data['uid']) ? $this->wormuser['uid'] : $data['uid'],'required' => true,'required_list' => 'number'],
            ['file' => 'mid','title' => '所属模型','type' => 'text','type_edit' => 'hidden','default' => $data['mid'],'required' => true,'required_list' => 'number'],
        ];
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
                    $res[] = ['file' => "groupid_{$v['id']}",'title' => "{$v['title']}{$moneyText}",'type' => 'text','type_group' => '订单结算','default' => '0','type_unit' => '元','required' => true,'required_list' => 'number'];
                }
            }
        }
        if(!empty($model['see_picurl'])){
            $res[] = ['file' => 'picurl','title' => '封面图','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'];
        }
        if(!empty($model['see_keyword'])){
            $res[] = ['file' => 'keywords','title' => '内容标签','type' => 'text','tip' => '多个标签请用英文“,”进行分割'];
        }
        if(!empty($model['see_description'])){
            $res[] = ['file' => 'description','title' => '内容简介','type' => 'textarea'];
        }
        return $res;
    }
    //  保存数据
    public function save()
    {
        if(!$this->isLogin()){
            $this->result('','10000','请登录');
        }
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->result('','0','非法访问');
        }
        if(!empty($this->webdb["{$this->basename}_edit_group"]) && !in_array($this->wormuser['u_groupid'],is_array($this->webdb["{$this->basename}_edit_group"]) ? $this->webdb["{$this->basename}_edit_group"] : explode(',',$this->webdb["{$this->basename}_edit_group"]))){
            $this->result('','0','你没有发表权限');
        }
        $data = Common::data_trim(input('post.'));
        if($this->request->isPut() && empty($data['id'])){
            $this->result('','0','非法访问');
        }
        $data['uid'] = empty($data['uid']) ? $this->wormuser['uid'] : $data['uid'];
        $form_list = $this->getBaseForm($data);
        $_data = empty($data['id']) ? [] : $this->model->getOne($data['mid'],$data['id']);
        $resdata = Common::SetReadFile($form_list,$data,$_data);
        if($resdata['code'] == '0'){
            $this->result('','0',$data['msg']);
        }
        $_data = array_merge($_data, $resdata['data']);
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
            $this->result('','0',$e->getError());
        }
        $_data['status'] ='1';
        if(!empty($this->webdb["{$this->basename}_status_group"]) && !in_array($this->wormuser['u_groupid'],is_array($this->webdb["{$this->basename}_status_group"]) ? $this->webdb["{$this->basename}_status_group"] : explode(',',$this->webdb["{$this->basename}_status_group"]))){
            $_data['status'] ='0';
        }
        $_data = $this->model->getEditAdd($_data);
        $res_title = !empty($this->getdata['id']) ? "编辑" : "添加";
        if($add = $this->model->setOne($_data)){
            $_model = Common::del_file($this->baseModelList, 'id', $add['mid']);
            $_model = $_model['0'];
            if($_model['order'] == '1'){
                //  生成价格信息
                $base = [
                    'parameter' => '0',
                    'stock_type' => $data['stock_type'],
                    'stock' => empty($data['stock_type']) ? '0' : $data['stock'],
                    'money_type' => '0',
                    'mid' => $add['mid'],
                    'aid' => $add['id'],
                    'groupid' => '0',
                    'chinacode' => '0',
                    'money' => $data['money'] * 100,
                    'money_zk' => $data['money_zk'] * 100,
                    'sale_one' => '0',
                    'sale_two' => '0',
                    'sale_three' => '0',
                ];
                //  检测是否启用三级分销
                if(!empty($this->webdb[$this->basename.'_sale_three'])){
                    $base['sale_one'] = empty($data['sale_one']) ? '0' : $data['sale_one'] * 100;
                    if($this->webdb[$this->basename.'_sale_three'] >= '2'){
                        $base['sale_two'] = empty($data['sale_two']) ? '0' : $data['sale_two'] * 100;
                    }
                    if($this->webdb[$this->basename.'_sale_three'] == '3'){
                        $base['sale_three'] = empty($data['sale_three']) ? '0' : $data['sale_three'] * 100;
                    }
                }
                $order[] = $base;
                if(in_array($_model['order_group'],['1','2','4'])) {
                    $usergroup = [];
                    if(in_array($_model['order_group'],['1','2'])) {
                        $usermodel = new \app\common\model\UserGroup();
                        $usergroup = $usermodel->getList(['group_type' => '1']);
                    }else {
                        $_part = Common::del_file($this->partModelList, 'id', $add['fid']);
                        $_chinalist = empty($_part['0']['chinalist']) ? [] : explode(',', $_part['0']['chinalist']);
                        $_chinalist = empty($_chinalist) ? [] : Common::del_null($_chinalist);
                        if(!empty($_chinalist)) {
                            $chinaModel = new Chinacode();
                            $_chinalist = $chinaModel->getList(['zoneid' => $_chinalist]);
                            $usergroup = NodeFormat::config(['id' => 'zoneid', 'pid' => 'parzoneid', 'title' => 'zonename'])->toList($_chinalist);
                        }
                    }
                    if(!empty($usergroup)){
                        $_vname = $_model['order_group'] == '4' ? 'chinacode' : 'groupid';
                        foreach ($usergroup as $k => $v) {
                            $v['id'] = $_model['order_group'] == '4' ? $v['zoneid'] : $v['id'];
                            if(!isset($data[$_vname.'_'.$v['id']])){
                                continue;
                            }
                            $money = empty($data[$_vname.'_'.$v['id']]) ? '0' : $data[$_vname.'_'.$v['id']] * 100;
                            $money_zk = empty($data[$_vname.'_'.$v['id'].'money_zk']) ? '0' : $data[$_vname.'_'.$v['id'].'money_zk'] * 100;
                            $sale_one = empty($data[$_vname.'sale_one']) ? '0' : $data[$_vname.'sale_one'] * 100;
                            $sale_two = empty($data[$_vname.'sale_two']) ? '0' : $data[$_vname.'sale_two'] * 100;
                            $sale_three = empty($data[$_vname.'sale_three']) ? '0' : $data[$_vname.'sale_three'] * 100;
                            $_v = [
                                'parameter' => '0',
                                'stock_type' => '0',
                                'stock' => '0',
                                'money_type' => '0',
                                'mid' => $add['mid'],
                                'aid' => $add['id'],
                                'groupid' => $_model['order_group'] == '4' ? '0' : $v['id'],
                                'chinacode' => $_model['order_group'] == '4' ? $v['id'] : '0',
                                'money' => $money,
                                'money_zk' => $money_zk,
                                'sale_one' => $sale_one,
                                'sale_two' => $sale_two,
                                'sale_three' => $sale_three,
                            ];
                            $order[] = $_v;
                        }
                    }
                }
                //  检测是否存在自订义规格
                if(!empty($data['parametermoney'])){
                    foreach ($data['parametermoney'] as $k => $v){
                        $_v = [
                            'parameter' => $v['parameter'],
                            'stock_type' => $data['stock_type'],
                            'stock' => empty($data['stock_type']) ? '0' : $v['stock'],
                            'money_type' => '0',
                            'mid' => $add['mid'],
                            'aid' => $add['id'],
                            'groupid' => '0',
                            'chinacode' => '0',
                            'money' => $v['money'] * 100,
                            'money_zk' => $v['money_zk'] * 100,
                            'sale_one' => empty($v['sale_one']) ? '0' : $v['sale_one'] * 100,
                            'sale_two' => empty($v['sale_two']) ? '0' : $v['sale_two'] * 100,
                            'sale_three' => empty($v['sale_three']) ? '0' : $v['sale_three'] * 100,
                        ];
                        $order[] = $_v;
                        if(!empty($usergroup) && in_array($_model['order_group'],['1','2','4'])) {
                            foreach ($usergroup as $k1 => $v1) {
                                $v1['id'] = $_model['order_group'] == '4' ? $v1['zoneid'] : $v1['id'];
                                if(!isset($v[$_vname.'_'.$v1['id']])){
                                    continue;
                                }
                                $money = empty($v[$_vname.'_'.$v1['id']]) ? '0' : $v[$_vname.'_'.$v1['id']] * 100;
                                $money_zk = empty($v[$_vname.'_'.$v1['id'].'money_zk']) ? '0' : $v[$_vname.'_'.$v1['id'].'money_zk'] * 100;
                                $sale_one = empty($v[$_vname.'_'.$v1['id'].'sale_one']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_one'] * 100;
                                $sale_two = empty($v[$_vname.'_'.$v1['id'].'sale_two']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_two'] * 100;
                                $sale_three = empty($v[$_vname.'_'.$v1['id'].'sale_three']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_three'] * 100;
                                $_v = [
                                    'parameter' => $v['parameter'],
                                    'stock_type' => '0',
                                    'stock' => '0',
                                    'money_type' => '0',
                                    'mid' => $add['mid'],
                                    'aid' => $add['id'],
                                    'groupid' => $_model['order_group'] == '4' ? '0' : $v1['id'],
                                    'chinacode' => $_model['order_group'] == '4' ? $v1['id'] : '0',
                                    'money' => $money,
                                    'money_zk' => $money_zk,
                                    'sale_one' => $sale_one,
                                    'sale_two' => $sale_two,
                                    'sale_three' => $sale_three,
                                ];
                                $order[] = $_v;
                            }
                        }
                    }
                }
                $orderModel = new Order();
                $orderModel->setOne($order,$this->basename,$_model['order_group'] == '4' ? 'chinacode' : 'groupid');
            }
            $this->result($add,'1',"{$res_title}成功");
        }
        $this->result('','0',"{$res_title}失败");
    }
    //  内容聚合
    public function household(){
        $getlist = $this->model->getHousehole($this->getMap());
        return $this->viewApiList($getlist);
    }
}