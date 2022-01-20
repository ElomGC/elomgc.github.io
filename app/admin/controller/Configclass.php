<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\common\model\ConfigClass as cxModel;

class Configclass extends AdminBase {
    use AddEditList;
    protected $list_temp = false;
    protected $validate = "app\\common\\validate\\AuthRuleclass";
    protected function initialize(){
        parent::initialize();
        $this->model = new cxModel();
        //  定义验证器
        if($this->request->action() == 'edit' && empty($this->getdata['id'])){
            $this->error("非法访问");
        }
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        $this->list_base['title'] = "参数分类";
        if($this->contauth['create']){
            $this->list_base['add'] = ['title' => '添加分类','opentitle' => "编辑参数分类",'uri' => url('create')->build(),'class' => 'cx-button-s cx-bg-green'];
        }
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center'],
            ['file' => 'title','title' => '参数分类','type' => 'edit','width' => '40%'],
            ['file' => 'sort','title' => '排序','type' => 'edit','textalign' => 'center','width' => '80',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1'],
        ];
        if($this->contauth['edit']){
            $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑参数分类",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
        }
        if($this->contauth['del']){
            $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
        }
        if(in_array($this->request->action(),['edit','create'])) {
            $this->list_base['uri'] = url('configclass/save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $this->list_base['title'] = false;
            $this->form_list = [
                ['file' => 'title','title' => '分类名称','type' => 'text','required' => true,],
                ['file' => 'uri','title' => '管理地址','type' => 'text','tip' => '一般为空',],
                ['file' => 'icon','title' => '选择图标','type' => 'icon'],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['1' => '启用','0' => '禁用'],'default' => '0']],
                ['file' => 'sort','title' => '排序','type' => 'text','type_edit' => 'number','default' => '0',],
               ];
            if($this->request->action() == 'edit'){
                $phsh = [
                    ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
                ];
                $this->form_list = array_merge($this->form_list,$phsh);
            }
        }
    }
    protected function getOrder(){
        return 'sort desc,id asc';
    }
}
