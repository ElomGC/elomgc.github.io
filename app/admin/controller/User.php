<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\common\model\UserGroup;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\Db;

class User extends AdminBase {
     use AddEditList;
     protected $list_temp = false;
     protected $FileModel;
     protected $GroupModel;
     protected $FileList;
     protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\User";
        $GroupModel = "app\\common\\model\\UserGroup";
        $filemodel = "app\\common\\model\\UserFile";
        $this->validate = "app\\common\\validate\\User";
         $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->FileModel = new $filemodel;
        $this->GroupModel = new $GroupModel;
        $this->list_base['id'] = "uid";
        if(!empty($this->getdata['id'])){
            $this->getdata['uid'] = $this->getdata['id'];
        }
        $this->getConf();
    }
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['uid']) && empty($data['u_password'])){
            $this->error("请输入密码");
        }
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        if(!empty($data['uid'])){
            if($data['uid'] == '1' && $data['status'] == '0'){
                $this->error("此用户不得禁用");
            }
        }
        $_data = [];
        if(!empty($this->FileList)){
            $_filelist = [];
            foreach ($this->FileList as $k => $v){
                $_filelist[] = Common::ReadFile($v);
            }
            $_data = empty($data['uid']) ? [] : Db::name('user_data')->whereUid($data['uid'])->find();
            $_data = empty($_data) ? [] : $_data;
            $_data = is_array($_data) ? $_data : $_data->toArray();
            $_id = empty($_data['id']) ? null : $_data['id'];
            $_data = Common::SetReadFile($_filelist,$data,empty($_data) ? [] : $_data);
            if($_data['code'] == '0'){
                $this->error($_data['msg']);
            }
            $_data = $_data['data'];
            if(!empty($_id)){
                $_data['id'] = $_id;
            }
        }
        $data = $this->model->getEditAdd($data);
        $data['_data'] = $_data;
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功",url('user/index')->build(),$add);
        }
        $this->error("{$res_title}失败");
    }
    //  生成表单参数
    protected function getConf(){
        $groupList = $this->GroupModel->getList($map = ['status' => 'a']);
        $_groupList = $_groupLists = [];
        foreach ($groupList as $k => $v){
            $_groupList[] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'value' => $v['id'],
                'group_type' => $v['group_type'],
                'uri' => url('user/index',['g' => $v['id']])->build(),
            ];
            $_groupLists[$v['id']] = $v['title'];
        }
        //  查询用户自订义字段
        $this->FileList = $this->FileModel->getList();
         //  重新定义标题
        $class_list = [['id' => '0','title' => '所有会员','value' => '','uri' => url('user/index',['g' => '0'])->build()]];
        $class_list = array_merge($class_list,$_groupList);
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
        }
        array_push($this->list_file,['file' => 'uid','title' => 'UID','type' => 'text','width' => '80','textalign' => 'center','fixed' => 'left'],
            ['file' => 'u_name','title' => '用户名','type' => 'text','width' => '150','fixed' => 'left'],
            ['file' => 'u_groupid','title' => '用户组','type' => 'radio','data' => $_groupLists,'width' => '120','textalign' => 'center'],
            ['file' => 'u_uname','title' => '姓名','type' => 'text','width' => '120'],
            ['file' => 'u_uniname','title' => '昵称','type' => 'text','width' => '150'],
            ['file' => 'u_sex','title' => '性别','type' => 'radio','textalign' => 'center','width' => '80','data' => ['0' => '女','1' => '男','2' => '-']]);

        //  查询需要在列表展示的字段
        $_balbelist = Common::del_file($this->FileList,'list_show','1');
        if(!empty($_balbelist)){
            foreach ($_balbelist as $k => $v){
                array_push($this->list_file,Common::ReadList($v));
            }
        }
        array_push($this->list_file,['file' => 'u_regtime','title' => '注册时间','type' => 'text','width' => '180','class' => 'cx-text-center'],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
        );
        if($this->request->action() == 'index'){
            $this->list_base['uri'] = url('getlist',empty($this->getdata['g']) ? [] : ['g' => $this->getdata['g']])->build();
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加用户','class' => 'cx-button-s cx-bg-green'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑用户信息",'uri' => url('edit',['id' => '__uid__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80','fixed' => 'right','full' => 'y'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right'];
                $this->list_top = [
                    ['title' => '批量删除', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                    ['title' => '回收站', 'class' => 'cx-button-s cx-bg-yellow cx-mag-r10', 'uri' => url('trash')->build()],
                ];
            }
            $this->list_search = [
                'fieldname' => 'u_groupid',
                'field' => $class_list,
                'input' => ['name' => 'key'],
                'uri' => url('getlist',['class' => $this->list_nav['default']])->build(),
            ];
        }else if(in_array($this->request->action(),['edit','create'])){
            $this->list_base['uri'] = url('user/save',$this->request->action() == 'create' ? [] : ['uid' => $this->getdata['id']]);
           //  开始生成表单参数
           $this->form_list = [
               ['file' => 'u_name','title' => '用户名','type' => 'text','required' => true,],
               ['file' => 'u_groupid','title' => '用户组','type' => 'select','required' => true,'data' => ['list' => $_groupLists,'default' => '0'],],
               ['file' => 'u_uniname','title' => '昵称','type' => 'text',],
               ['file' => 'u_uname','title' => '姓名','type' => 'text',],
               ['file' => 'u_sex','title' => '性别','type' => 'radio','data' => ['list' => ['1' => '男','0' => '女','2' => '保密'],'default' => '0']],
               ['file' => 'u_phone','title' => '手机','type' => 'text',],
               ['file' => 'u_icon','title' => '头像','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1',],
               ['file' => 'u_mail','title' => '邮箱','type' => 'text',],
               ['file' => 'u_password','title' => '密码','type' => 'text','type_edit' => 'password','tip' => '如不修改密码请留空',],
               ['file' => 'u_bdy','title' => '生日','type' => 'text','type_edit' => 'date',],
               ['file' => 'u_card','title' => '身份证号','type' => 'text',],
               ['file' => 'u_cardimg','title' => '身份证图片','type' => 'upload','type_edit' => 'array','upload_filenum' => '2',],
               ['file' => 'status','title' => '启用','type' => 'radio','data' => ['list' => ['1' => '启用','0' => '禁用'],'default' => '1']],
           ];
           if(!empty($this->FileList)){
               $_filelist = [];
               foreach ($this->FileList as $k => $v){
                   $_filelist[] = Common::ReadFile($v);
               }
               $this->form_list = array_merge($this->form_list,$_filelist);
           }
            if($this->request->action() == 'edit'){
                $phsh = array(
                    ['file' => 'uid','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
                    );
                $this->form_list = array_merge($this->form_list,$phsh);
                $this->form_list['0']['disabled'] = true;
            }
        }elseif($this->request->action() == 'trash'){
            $this->list_base['uri'] = url('getlist',['del_time' => '1001','status' => 'a'])->build();
            $this->list_rightbtn = [
                ['type' => 'trash','id_edit' => 'uid','title' => '还原'],
                ['type' => 'del','id_edit' => 'uid'],
            ];
            $this->list_top = [
                ['title' => '用户列表', 'class' => 'cx-button-s cx-bg-blue cx-mag-r10', 'uri' => url('index')->build()],
                ['title' => '批量还原', 'class' => 'cx-button-s cx-bg-green cx-mag-r10', 'uri' => url('trashone')->build()],
                ['title' => '批量删除', 'class' => 'cx-button-s cx-bg-yellow', 'uri' => url('pdel')->build()],
            ];
        }
    }
    protected function getMap(){
         if(!empty($this->getdata['g'])){
             $this->getdata['u_groupid'] = $this->getdata['g'];
         }
        return array_merge(parent::getMap(),$this->getdata);
    }
    protected function getOrder(){
        return "uid asc";
    }
    public function fastswitch()
    {
        if(!$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['uid']) || empty($data['field'])){
            $this->error("非法访问");
        }
        if($data['uid'] == '1' && $data['value'] == '0'){
            $this->error("此用户不得禁用");
        }
        $old = $this->model->whereUid($data['uid'])->find();
        $old[$data['field']] = $data['value'];
        if($old->save()){
            $this->success('处理成功');
        }
        $this->error("处理失败");
    }
    public function del()
    {
        $data = Common::data_trim(input('post.'));
        if(!$this->request->isDelete() || empty($data['uid'])){
            $this->error("非法访问");
        }
        if($data['uid'] == '1'){
            $this->error("此用户禁止删除");
        }
        $res = $this->model->DeleteOne($data['uid']);
        if($res === true || !empty($res['code'])){
            $this->success("删除成功");
        }
        $this->error(empty($res['msg']) ? "删除失败" : $res['msg']);
    }
}