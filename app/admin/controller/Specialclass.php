<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use worm\NodeFormat;

class Specialclass extends AdminBase
{
    use AddEditList;
    protected $validatename = ['edit' => 'Specialclassedit','add' => 'Specialclassadd',];
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\SpecialClassBase";
        $this->validate = "app\\common\\validate\\Nav";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->getConf();
    }
    protected function getConf(){
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center','fixed' => 'left'],
            ['file' => 'title','title' => '分类名称','type' => $this->contauth['edit'] ? 'edit' : 'text','width' => '40%'],
            ['file' => 'sort','title' => '排序','type' => $this->contauth['edit'] ? 'edit' : 'text','textalign' => 'center','width' => '80'],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
        ];
        if($this->request->action() == 'index'){
            $this->list_base['page'] = "1";
            $this->list_base['open'] = 'false';
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加分类','class' => 'cx-button-s cx-bg-green'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑友情链接分类",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80','fixed' => 'right'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right'];
            }
        }elseif (in_array($this->request->action(),['edit','create'])){
            $this->list_base['uri'] = url('save',$this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            //  获取原有分类
            $_list = $this->model->getList();
            $_list = NodeFormat::toList($_list);
            if(!empty($this->getdata['pid'])){
                $_ids = NodeFormat::getChildsId($_list,$this->getdata['pid']);
                array_push($_ids,$this->getdata['pid']);
                $_list = Common::del_file($_list,'id',$_ids,false);
            }
            $_listdb = [];
            if(!empty($_list)){
                foreach ($_list as $k => $v){
                    if(!empty($this->getdata['id']) && $v['id'] == $this->getdata['id']){
                        continue;
                    }
                    $_listdb[$v['id']] = $v['title_display'];
                }
            }
            $this->form_list = [
                ['file' => 'pid','title' => '上级分类','text_edit' => '顶级分类','type' => 'select','data' => ['list' => $_listdb,'default' => empty($this->getdata['pid']) ? '0' : (int) $this->getdata['pid']],'tip' => "如不选择则为顶级栏目"],
                ['file' => 'title','title' => '分类名称','type' => 'text','required' => true],
                ['file' => 'sort','title' => '排序值','type' => 'text','required' => true,'default' => '0'],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' =>['list' => ['0' => '关闭分类','1' => '启用分类',],'default' => '1']],
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
}