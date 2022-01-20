<?php
declare(strict_types = 1);
namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;

class User extends ApiBase {
    use AddEditList;
    protected $FileModel;
    protected $FileList;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\User";
        $filemodel = "app\\common\\model\\UserFile";
        $this->validate = "app\\common\\validate\\User";
        $this->model = new $models;
        $this->FileModel = new $filemodel;
        if(in_array($this->request->action(),['edit','save'])){
            $this->FileList = $this->FileModel->getList();
        }
    }
    protected function getMap()
    {
        $map = [
            'uid' => empty($this->getdata['uid']) ? '' : $this->getdata['uid'],
            'key' => empty($this->getdata['key']) ? '' : $this->getdata['key'],
            'u_groupid' => empty($this->getdata['u_groupid']) ? '' : explode('|',$this->getdata['u_groupid']),
            'limit' => empty($this->getdata['limit']) ? '' : $this->getdata['limit'],
            'page' => empty($this->getdata['page']) ? '' : $this->getdata['page'],
        ];
        return $map;
    }

    public function index($uid)
    {
        $this->getdata['uid'] = empty($this->getdata['uid']) ? $this->wormuser['uid'] : $this->getdata['uid'];
        $getlist = getUser($this->getdata['uid']);
        if($getlist['status'] != '1' || $getlist['del_time'] > '0'){
            $this->result('','0','用户不存在');
        }
        if($getlist['uid'] == $this->wormuser['uid']){
            $getlist = Upload::editaddApi($getlist,false);
            $utoken = $this->res_token($getlist);
            $getlist['token'] = $utoken['token'];
            $getlist['times'] = $utoken['times'];
        }
        $this->result($getlist,'1','获取成功');
    }
    public function edit(){
        if(!$this->isLogin()){
            $this->result('','0','非法访问');
        }
        $form_list = [
            ['file' => 'u_name','title' => '用户名','type' => 'text','disabled' => true,'default' => $this->wormuser['u_name']],
            ['file' => 'u_uniname','title' => '昵称','type' => 'text','default' => $this->wormuser['u_uniname']],
            ['file' => 'u_uname','title' => '姓名','type' => 'text','default' => $this->wormuser['u_uname']],
            ['file' => 'u_sex','title' => '性别','type' => 'radio','data' => ['list' => ['1' => '男','0' => '女','2' => '保密'],'default' => $this->wormuser['u_sex']]],
            ['file' => 'u_phone','title' => '手机','type' => 'text','default' => $this->wormuser['u_phone']],
            ['file' => 'u_icon','title' => '头像','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1','default' => $this->wormuser['u_icon']],
            ['file' => 'u_mail','title' => '邮箱','type' => 'text','default' => $this->wormuser['u_mail']],
            ['file' => 'u_password','title' => '密码','type' => 'text','type_edit' => 'password','tip' => '如不修改密码请留空',],
            ['file' => 'u_bdy','title' => '生日','type' => 'text','type_edit' => 'date','default' => $this->wormuser['u_bdy']],
            ['file' => 'u_card','title' => '身份证号','type' => 'text','default' => $this->wormuser['u_card']],
            ['file' => 'u_cardimg','title' => '身份证图片','type' => 'upload','type_edit' => 'array','upload_filenum' => '2','default' => $this->wormuser['u_cardimg']],
            ['file' => 'uid','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number','default' => $this->wormuser['uid']],
            ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
        ];
        if(!empty($this->FileList)){
            $_filelist = [];
            foreach ($this->FileList as $k => $v){
                $_filelist[] = Common::ReadFile($v);
            }
            $this->form_list = array_merge($this->form_list,$_filelist);
        }
        return $this->viewApiRead($form_list);
    }
    public function save()
    {
        if(!$this->isLogin()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        if($data['uid'] != $this->wormuser['uid']){
            $this->result('','0','非法访问');
        }
        $_data = [];
        if(!empty($this->FileList)){
            $_data = empty($data['uid']) ? [] : Db::name('user_data')->whereUid($data['uid'])->find();
            $_data = empty($_data) ? [] : $_data;
            $_data = is_array($_data) ? $_data : $_data->toArray();
            $_id = empty($_data['id']) ? null : $_data['id'];
            $_updata = array_diff_key($_data,$data);
            if(!empty($_updata)){
                $_updata = array_keys($_updata);
                $this->FileList = Common::del_file($this->FileList,'file',$_updata,true);
            }
            $_data = Common::SetReadFile($this->FileList,$data,empty($_data) ? [] : $_data);
            if($_data['code'] == '0'){
                $this->result('','0',$_data['msg']);
            }
            $_data = $_data['data'];
            if(!empty($_data) && !empty($_id)){
                $_data['id'] = $_id;
            }
        }
        $data = $this->model->getEditAdd($data);
        $data['_data'] = $_data;
        if($add = $this->model->setOne($data)){
            $user = $this->model->getOne(['uid' => $this->wormuser['uid']]);
            $user = Upload::editaddApi($user,false);
            $utoken = $this->res_token($user);
            $user['token'] = $utoken['token'];
            $user['times'] = $utoken['times'];
            $this->result($user,'1',"编辑成功");
        }
        $this->result('','0',"编辑失败");
    }
    //  自动识别身份证信息
    public function getcard($card){
        if(!Common::isIdCard($card)){
            $this->result([],'0','身份证号非法');
        }
        $res = [
            'sex' => Common::get_sex($card),
            'ubdy' => Common::get_ubdy($card),
            'age' => Common::get_age($card),
            'zodiac' => Common::get_zodiac($card),
            'starsign' => Common::get_starsign($card),
            'address' => Common::get_address($card),
        ];
        $this->result($res,'1','获取成功');
    }
    //  批量查询用户
    public function userlist(){
        $map = $this->getMap();
        if(!empty($this->getdata['r']) && $this->getdata['r'] == 'a'){
            $map['uid'] = empty($map['uid']) ? $this->wormuser['uid'] : $map['uid'];
        }
        $res = $this->model->getList($map);
        return $this->viewApiList($res);
    }
}