<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use worm\NodeFormat;

class Special extends AdminBase
{
    use AddEditList;
    protected $classModel;
    protected $classModelList;
    protected $validatename = ['edit' => 'Specialclassedit','add' => 'Specialclassadd',];
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\Special";
        $this->model = new $models;
        $models = "app\\common\\model\\SpecialClassBase";
        $this->classModel = new $models;
        $this->validate = "app\\common\\validate\\Nav";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf()
    {
        $_ClassList = $this->classModel->getList();
        if(empty($_ClassList)){
            $this->error("请先添加分类");
        }
        $this->ClassList = NodeFormat::toList($_ClassList);
        $_listdb = [];
        $_field = [['title' => '全部','value'=>'']];
        foreach ($this->ClassList as $k => $v){
            $_listdb[$v['id']] = $v['title'];
            $_field[] = ['title' => $v['title'],'value' => $v['id']];
        }
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
        }
        array_push($this->list_file,['file' => 'id', 'title' => 'ID', 'type' => 'text', 'width' => '80', 'textalign' => 'center', 'fixed' => 'left'],
            ['file' => 'cid','title' => '所属分类','type' => 'radio','data' => $_listdb,'class' => 'cx-text-center','width' => '120'],
            ['file' => 'title', 'title' => '专题名称', 'type' => $this->contauth['edit'] ? 'edit' : 'text','width' => '40%'],
            ['file' => 'article_num','title' => '内容数量','type' => 'link','textalign' => 'center','width' => '120','uri' => url('specialarticle/index',['sid' => '__id__'])->build()],
            ['file' => 'sort', 'title' => '排序', 'type' => $this->contauth['edit'] ? 'edit' : 'text', 'textalign' => 'center', 'width' => '80'],
            ['file' => 'status', 'title' => '状态', 'type' => 'switch', 'text' => '启用|禁用', 'textalign' => 'center', 'width' => '100', 'default' => '1']);
        if($this->request->action() == 'index'){
            $this->list_search = [
                'fieldname' => 'cid',
                'field' => $_field,
                'uri' => url('getlist')->build(),
            ];
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加专题','class' => 'cx-button-s cx-bg-green'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑专题",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80','fixed' => 'right'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right'];
                $this->list_top = [
                    ['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                ];
            }
        }elseif(in_array($this->request->action(),['create','edit','save'])) {
            if(in_array($this->request->action(),['create','edit'])){
                $this->list_base['uri'] = url('save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            }
            $_listdb = [];
            foreach ($this->ClassList as $k => $v){
                $_listdb[$v['id']] = $v['title_display'];
            }
            $this->form_list = [
                ['file' => 'cid','title' => '所属分类','text_edit' => '所属分类','type' => 'select','required' => true,'data' => ['list' => $_listdb,'default' => empty($this->getdata['pid']) ? '0' : (int) $this->getdata['pid']],'tip' => "如不选择则为顶级栏目"],
                ['file' => 'title','title' => '专题名称','type' => 'text','required' => true],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' =>['list' => ['0' => '关闭专题','1' => '启用专题',],'default' => '1']],
                ['file' => 'keywords','title' => 'SEO关键词','type' => 'text','tip' => '多个关键词请用英文’,‘进行分割，推荐80字以内',],
                ['file' => 'description','title' => 'SEO描述','type' => 'textarea','tip' => '推荐200字以内',],
                ['file' => 'banber','title' => 'BANBER','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'],
                ['file' => 'logo','title' => 'LOGO','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'],
                ['file' => 'temp_late','title' => '自订义风格','type' => 'text'],
                ['file' => 'temp_head','title' => '自订义头部','type' => 'text','tip' => '独立头部风格，如：default/new_head.htm'],
                ['file' => 'temp_list','title' => '自订义列表','type' => 'text','tip' => '独立列表页风格，如：default/new_list.htm'],
                ['file' => 'temp_foot','title' => '自订义底部','type' => 'text','tip' => '独立底部风格，如：default/new_foot.htm'],
                ['file' => 'sort','title' => '排序值','type' => 'text','required' => true,'default' => '0'],
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
    protected function getMap()
    {
        $map = [
            'status' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status']
        ];
        return array_merge(parent::getMap(),$map); // TODO: Change the autogenerated stub
    }
}