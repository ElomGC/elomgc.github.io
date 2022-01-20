<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\AuthRuleclass;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use worm\NodeFormat;

class Authrule extends AdminBase {
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\AuthRule";
        $this->validate = "app\\common\\validate\\AuthRule";
        $this->model = new $models;
        $this->getConf();
    }
    protected function getConf(){
        $this->list_base['uri'] = url('getlist')->build();
        $this->list_base['page'] = '1';
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center'],
            ['file' => 'title','title' => '权限名称','type' => 'text','width' => '20%'],
            ['file' => 'name','title' => '权限地址','type' => 'edit','width' => '20%'],
            ['file' => 'sort','title' => '排序','type' => 'edit','textalign' => 'center','class_value' => 'cx-text-center','width' => '80',],
            ['file' => 'menusee','title' => '菜单','type' => 'switch','text' => '显示|隐藏','textalign' => 'center','width' => '100','default' => '1',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1'],
            ['filed' => 'create','title' => '子权限','type' => 'btn','text' => true,'open' => true,'opentitle' => "添加子权限",'uri' => url('create',['pid' => '__id__'])->build(),'icon' => 'cx-iconadd cx-text-f16','class' => 'cx-text-black','textalign' => 'center','width' => '80'],
            ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑权限",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'],
            ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80']
        ];
        $this->list_search = [
            'field' => array(
                ['title' => "权限名称",'value' => "title"],
                ['title' => "权限地址",'value' => "name"]
            ),
            'fieldname' => 'field',
            'uri' => url('search')->build(),
        ];
        $classModel = new AuthRuleclass();
        $class_list = $classModel->getList();
        if(!empty($this->getdata['pid'])){
            $this->getdata['t'] = $this->model->whereId($this->getdata['pid'])->value('type_class');
        }
        $this->list_nav = [
            'list' => $class_list,
            'default' => empty($this->getdata['t']) ? $class_list['0']['id'] : $this->getdata['t'],
        ];
        //  重新定义标题
        if($this->request->action() == 'edit' || $this->request->action() == 'create'){
            $this->list_base['uri'] = url('authrule/save',$this->request->action() == 'create' ? ['t' => $this->list_nav['default']] : ['id' => $this->getdata['id']]);
            //  格式化上级权限选择分类
            $listdb = $this->model->getList();
            if(!empty($this->getdata['pid'])){
                $ids = NodeFormat::getChildsId($listdb,$this->getdata['pid']);
                foreach ($listdb as $k => $v){
                    if(in_array($v['id'],$ids)){
                        unset($listdb[$k]);
                    }
                    continue;
                }
            }else if(!empty($this->getdata['id'])){
                $ids = NodeFormat::getChildsId($listdb,$this->getdata['id']);
                array_push($ids,$this->getdata['id']);
                foreach ($listdb as $k => $v){
                    if(in_array($v['id'],$ids)){
                        unset($listdb[$k]);
                    }
                    continue;
                }
            }
            $listdb = NodeFormat::toList($listdb);
            $_listdb = array();
            if(!empty($listdb)){
                foreach ($listdb as $k => $v){
                    $_listdb[$v['id']] = $v['title_display'];
                }
                $listdb = $_listdb;
                unset($_listdb);
            }
            //  格式化权限类型选择
            $type_list = array();
            if(!empty($this->list_nav['list'])){
                foreach ($this->list_nav['list'] as $k => $v){
                    $type_list[$v['id']] = $v['title'];
                }
            }
            //  开始生成表单参数
            $this->form_list = array(
                ['file' => 'pid','title' => '上级权限','type' => 'select','data' => array('list' => $listdb,'default' => empty($this->getdata['pid']) ? '0' : (int) $this->getdata['pid']),'tip' => "如不选择则为顶级权限",],
                ['file' => 'title','title' => '权限名称','type' => 'text','required' => true,],
                ['file' => 'icon','title' => '权限图标','type' => 'icon',],
                ['file' => 'name','title' => '权限地址','type' => 'text','required' => true,],
                ['file' => 'open','title' => '开发者模式','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '0')],
                ['file' => 'menusee','title' => '菜单','type' => 'radio','data' => array('list' => array('0' => '隐藏','1' => '显示'),'default' => '0',),],
                ['file' => 'topsee','title' => '顶部显示','type' => 'radio','data' => array('list' => array('0' => '隐藏','1' => '显示'),'default' => '0',),],
                ['file' => 'status','title' => '状态','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '1')],
                ['file' => 'type_class','title' => '权限类型','type' => 'radio','data' => array('list' => $type_list,'default' => empty($this->getdata['t']) ? $this->list_nav['list']['0']['id'] : (int) $this->getdata['t'],),],
                ['file' => 'condition','title' => '自订义规则','type' => 'text','tip' => '一般为空（不懂程序请勿填写）',],
                ['file' => 'sort','title' => '排序','type' => 'text','type_edit' => 'number','default' => '0','required' => true,'required_list' => 'number','tip' => '数字越大排序越靠前',]
            );
            if($this->request->action() == 'edit'){
                $phsh = array(
                    ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
                );
                $this->form_list = array_merge($this->form_list,$phsh);
            }
        }
    }
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功",url('index')->build(),$add);
        }
        $this->error("{$res_title}失败");
    }
    protected function getMap()
    {
        $map = [
            'type_class' => empty($this->getdata['t']) ? '' : $this->getdata['t'],
        ];
        if($this->wormuser['u_groupid'] == '1'){
            $map['open'] = '1';
            $map['status'] = 'a';
        }
        return array_merge(parent::getMap(),$map);
    }

}