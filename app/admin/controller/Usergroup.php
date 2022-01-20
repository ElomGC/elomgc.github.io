<?php
declare(strict_types = 1);
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use worm\NodeFormat;

class Usergroup extends AdminBase {

    use AddEditList;
    protected $list_temp = false;
    protected $list_page = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\UserGroup";
        $this->validate = "app\\common\\validate\\UserGroup";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->list_base['page'] = '2';
        $this->list_base['open'] = 'false';
        $this->getConf();
    }
    public function getConf(){
        //  重新定义标题
        $this->list_base['title'] = "用户组";
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','textalign' => 'center','fixed' => 'left','width' => '80'],
            ['file' => 'title','title' => '用户组名称','type' => 'text','width' => '40%'],
            ['file' => 'group_type','title' => '类型','type' => 'radio','data' => ['0' => '系统组','1' => '会员组'],'textalign' => 'center','width' => '120'],
            ['file' => 'group_up','title' => '升级积分','type' => 'text','width' => '100'],
            ['file' => 'sort','title' => '排序','textalign' => 'center','width' => '80','type' => 'edit',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1'],
        ];
        if($this->request->action() == 'index') {
            $this->list_base['uri'] = url('getlist')->build();
            if($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加用户组', 'class' => 'cx-button-s cx-bg-green'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['file' => 'group_type','title' => '后台权限','type' => 'radio','type_edit' => 'link','text' => true,'open' => true,'opentitle' => "分配后台权限",'textalign' => 'center','width' => '120','data' => ['0' => ['uri' => url('rules',['id' => '__id__','t' => '1'])->build(),'icon' => 'cx-iconquanxianguanli1 cx-text-f16','class' => 'cx-text-green',]]];
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑栏目",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center', 'fixed' => 'right','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center', 'fixed' => 'right','width' => '80'];
            }
        }else if($this->request->action() == 'edit' || $this->request->action() == 'create'){
            $this->list_base['uri'] = url('usergroup/save',$this->request->action() == 'create' ? ['pid' => empty($this->getdata['pid']) ? '0' : $this->getdata['pid']] : ['id' => $this->getdata['id']]);
            //  开始生成表单参数
            $this->form_list = [
                ['file' => 'title','title' => '用户组名称','type' => 'text','required' => true,],
                ['file' => 'group_icon','title' => '用户组图标','type' => 'icon',],
                ['file' => 'group_type','title' => '用户组类型','type' => 'radio','data' => ['list' => ['1' => '会员组','0' => '系统组'],'default' => '1'],'file_link' => ['0' => ['group_admin'],'1' => ['group_up','group_money','group_space']]],
                ['file' => 'group_up','title' => '升级积分','type' => 'text','type_unit' => '积分','default' => '0',],
//                ['file' => 'group_money','title' => '积分类型','type' => 'text',],
                ['file' => 'group_admin','title' => '后台权限','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '0')],
                ['file' => 'group_space','title' => '储存空间','type' => 'text','type_unit' => 'MB','default' => '0',],
                ['file' => 'status','title' => '状态','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '1')],
                ['file' => 'sort','title' => '排序','type' => 'text','type_edit' => 'number','default' => '0','required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',]
            ];
            if($this->request->action() == 'edit') {
                $phsh =[
                    ['file' => 'id', 'title' => 'ID', 'type' => 'text', 'type_edit' => 'hidden', 'required' => true, 'required_list' => 'number',],
                    ['file' => '_method', 'title' => 'ID', 'type' => 'text', 'type_edit' => 'hidden', 'required' => true, 'default' => 'PUT',]
                ];
                $this->form_list = array_merge($this->form_list, $phsh);
            }
        }
    }
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        $data = $this->model->getEditAdd($data);
        if(!empty($data['id']) && $data['id'] == '1' && $data['status'] == '0'){
            $this->error("此用户组不可禁用");
        }
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功",url('usergroup/index')->build(),$add);
        }
        $this->error("{$res_title}失败");
    }
    public function rules(){
        switch ($this->getdata['t']){
            case '1':
                $this->list_base['files'] = 'rules';
                break;
            case '2':
                $this->list_base['files'] = 'mules';
                break;
            case '3':
                $this->list_base['files'] = 'hules';
                break;
            case '4':
                $this->list_base['files'] = 'aules';
                break;
        }
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            $data[$data['files']] = empty($data['auth']) ? null : $data['auth'];
            $data = $this->model->getEditAdd($data);
            if($add = $this->model->setOne($data)){
                $this->success("修改权限成功",url('usergroup/index')->build(),$add);
            }
            $this->error("修改权限失败");
        }
        $this->list_temp = 'authrule:add';
        $this->list_base['id'] = $map['id'] = $this->getdata['id'];
        $this->list_base['uri'] = url('rules',['id' => $this->getdata['id'],'t' =>$this->getdata['t']])->build();
        $old = $this->model->getList($map);
        $old = $old['0'];
        if($old['id'] == '1') {
            $aModel = new \app\common\model\AuthRule();
            $listdb = $aModel->getList(['type_class' => $this->getdata['t'],'status' => 'a']);
        }else{
            $listdb = $this->WormAuth();
            $listdb = $listdb[$this->getdata['t']]['c'];
        }
        $listdb = NodeFormat::toLayer($listdb);
        $_listdb = $this->authlist($listdb,empty($old[$this->list_base['files']]) ? [] : $old[$this->list_base['files']]);
        return $this->viewAdminAdd($_listdb);
    }
    protected function getOrder(){
        return "sort desc,id asc";
    }
    protected function getMap()
    {
        $map = [
            'status' => 'a'
        ];
        return array_merge(parent::getMap(),$map); // TODO: Change the autogenerated stub
    }
    protected function authlist($data,$old=[],$level = '0'){
        $temps = $_temps = '';
        foreach ($data as $k => $v){
            $checked = in_array($v['id'],$old) ? "checked='checked'" : '';
            $temps .= "<h3><input type='checkbox' name='auth[]' value='{$v['id']}' title='{$v['title']}' {$checked} lay-skin='primary' lay-filter='authcheckbox'></h3>";
            if(!empty($v['node'])){
                $temps .= $this->authlist($v['node'],$old,$level + 1);
            }
        }
        $class = $level == '0' ? 'cx-fex-l cx-fex-column' : 'cx-fex-l';
        $_temps = $level > '2' ? $temps : "<div class='layout groupauth-list {$class}'>{$temps}</div>";
        return $_temps;
    }
}