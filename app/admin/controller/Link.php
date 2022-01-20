<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class Link extends AdminBase {
    use AddEditList;
    protected $classModel;
    protected $classModelList;
    protected function initialize(){
        parent::initialize();
        $classmodels = "app\\common\\model\\LinkClass";
        $models = "app\\common\\model\\Link";
        $this->validate = "app\\common\\validate\\Nav";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->classModel = new $classmodels;
        $this->getConf();
    }
    protected function getConf(){
        $this->classModelList = $this->classModel->getList(['status' => '1']);
        if(empty($this->classModelList)){
            $this->error("请先添加友情链接分类",url('linkclass/index')->build());
        }
        $_classlist = $_navclasslist = [];
        foreach ($this->classModelList as $k => $v){
            $_classlist[] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'uri' => url('link/index',['class' => $v['id']])->build(),
            ];
        }
        $this->getdata['class'] = empty($this->getdata['class']) ? $_classlist['0']['id'] : $this->getdata['class'];
        $this->list_nav = count($_classlist) < 2 ? [] : [
            'list' => $_classlist,
            'default' => empty($this->getdata['class']) ? $_classlist['0']['id'] : $this->getdata['class'],
        ];
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','width' => '40']];
        }
        array_push($this->list_file,['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center'],
            ['file' => 'title', 'title' => '链接名称', 'type' =>$this->contauth['edit'] ? 'edit' : 'text','width' => '30%'],
            ['file' => 'uri', 'title' => '链接地址', 'type' =>$this->contauth['edit'] ? 'edit' : 'text','width' => '30%'],
            ['file' => 'sort', 'title' => '排序', 'type' =>$this->contauth['edit'] ? 'edit' : 'text', 'textalign' => 'center','width' => '80'],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
        );
        if($this->request->action() == 'index'){
            $this->list_base['page'] = "2";
            $this->list_base['open'] = 'false';
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加链接','class' => 'cx-button-s cx-bg-green','uri' => url('create',['class' => $this->getdata['class']])->build()];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑链接",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
                $this->list_top = [
                    ['title' => '批量删除','event' => 'pdel','class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                ];
            }
        }else if($this->request->action() == 'edit' || $this->request->action() == 'create') {
            $this->list_base['uri'] = url('save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $this->form_list = [
                ['file' => 'title','title' => '链接名称','type' => 'text','required' => true,],
                ['file' => 'logo','title' => 'LOGO','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'],
                ['file' => 'uri','title' => '链接地址','type' => 'text','required' => true],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['0' => '禁用','1' => '启用'],'default' => '1']],
                ['file' => 'sort','title' => '排序','type' => 'text','type_edit' => 'number','default' => '0','required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',],
                ['file' => 'class','title' => '排序','type' => 'text','type_edit' => 'hidden','default' => $this->getdata['class'],'required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',]
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
    protected function getMap(){
        $map = [
            'class' => [!isset($this->getdata['class']) ? $this->classModelList['0']['id'] : $this->getdata['class']]
        ];
        return array_merge(parent::getMap(),$map);
    }
}