<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;

class Nav extends AdminBase {
    use AddEditList;
    protected $classModel;
    protected $classModelList;
    protected function initialize(){
        parent::initialize();
        $classmodels = "app\\common\\model\\NavClass";
        $models = "app\\common\\model\\Nav";
        $this->validate = "app\\common\\validate\\Nav";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->classModel = new $classmodels;
        $this->getConf();
    }
    protected function getConf(){
        $this->classModelList = $this->classModel->getList(['status' => '1']);
        if(empty($this->classModelList)){
            $this->error("请先添加导航分类",url('navclass/index')->build());
        }
        $_classlist = $_navclasslist = [];
        foreach ($this->classModelList as $k => $v){
            $_classlist[] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'uri' => url('nav/index',['class' => $v['id']])->build(),
            ];
        }
        $this->getdata['class'] = empty($this->getdata['class']) ? $_classlist['0']['id'] : $this->getdata['class'];
        if($this->request->action() == 'edit' || $this->request->action() == 'create') {
            $navlist = $this->model->getList(['status' => 'a']);
            if(!empty($this->getdata['id'])){
                $_navlist = Common::del_file($navlist,'id',$this->getdata['id']);
                $this->getdata['class'] = $_navlist['0']['class'];
            }else if(!empty($this->getdata['pid'])){
                $_navlist = Common::del_file($navlist,'id',$this->getdata['pid']);
                $this->getdata['class'] = $_navlist['0']['class'];
            }
        }
        $_class = Common::del_file($this->classModelList,'id',$this->getdata['class']);
        $_class = $_class['0'];
        $this->list_nav = count($_classlist) < 2 ? [] : [
            'list' => $_classlist,
            'default' => empty($this->getdata['class']) ? $_classlist['0']['id'] : $this->getdata['class'],
        ];
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center','fixed' => 'left'],
            ['file' => 'title', 'title' => '导航名称', 'type' => 'text','width' => '30%'],
            ['file' => 'uri', 'title' => '链接地址', 'type' => 'edit','width' => '30%'],
            ['file' => 'sort', 'title' => '排序', 'type' => 'edit', 'textalign' => 'center', 'width' => '80'],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
        ];
       if($this->request->action() == 'index'){
           $this->list_base['page'] = "1";
           $this->list_base['open'] = 'false';
           $this->list_base['uri'] = url('getlist',['class' => $this->getdata['class']])->build();
           if ($this->contauth['create']) {
               $this->list_base['add'] = ['title' => '添加导航','class' => 'cx-button-s cx-bg-green','uri' => url('nav/create',['class' => $this->getdata['class']])->build()];
               if($_class['level'] == '1') {
                   $this->list_file[] = ['filed' =>'create', 'title' => '子导航','type' => 'btn','text' => true,'open' => true,'opentitle' => "添加子导航",'uri' => url('create',['pid' => '__id__'])->build(),'icon' => 'cx-iconadd cx-text-f16','class' => 'cx-text-black','textalign' => 'center','width' => '80'];
               }
           }
           if($this->contauth['edit']){
               $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑导航",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80','fixed' => 'right'];
           }
           if($this->contauth['del']){
               $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right'];
           }
       }else if($this->request->action() == 'edit' || $this->request->action() == 'create') {
            $this->list_base['uri'] = url('save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $this->list_base['title'] = $this->request->action() == 'create' ? "添加{$_class['title']}" : "编辑{$_class['title']}";
            $this->form_list = [
                ['file' => 'title','title' => '导航名称','type' => 'text','required' => true,],
                ['file' => 'icon','title' => '导航图标','type' => 'icon',],
                ['file' => 'uri','title' => '链接地址','type' => 'text','required' => true],
                ['file' => 'target','title' => '新窗口打开','type' => 'radio','data' => ['list' => ['0' => '当前窗口打开','1' => '新窗口打开'],'default' => '0']],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['0' => '禁用','1' => '启用'],'default' => '1']],
                ['file' => 'sort','title' => '排序','type' => 'text','type_edit' => 'number','default' => '0','required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',],
                ['file' => 'class','title' => '排序','type' => 'text','type_edit' => 'hidden','default' => $this->getdata['class'],'required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',]
            ];
            if($_class['level'] == '1'){
                $navlist = Common::del_file($navlist,'class',$this->getdata['class']);
                $navlist = Common::del_file($navlist,'pid','0');
                $_navlist = [];
                if(!empty($navlist)){
                    foreach ($navlist as $k => $v){
                        if(!empty($this->getdata['id']) && $v['id'] == $this->getdata['id'] || empty($v['status'])){
                            continue;
                        }
                        $_navlist[$v['id']] = $v['title'];
                    }
                }
                $phsh[] = ['file' => 'pid','title' => '上级导航','title_edit' => '顶级导航','type' => 'select','data' => ['list' => $_navlist,'default' => empty($this->getdata['pid']) ? '0' : (int) $this->getdata['pid']],'tip' => "如不选择则为顶级导航",];
                $this->form_list = array_merge($phsh,$this->form_list);
            }
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
            'class' => !isset($this->getdata['class']) ? $this->classModelList['0']['id'] : $this->getdata['class'],
            'status' => 'a'
        ];
        return array_merge(parent::getMap(),$map);
    }
}