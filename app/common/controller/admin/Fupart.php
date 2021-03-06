<?php
declare(strict_types = 1);

namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\model\UserGroup;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use worm\NodeFormat;

class Fupart extends AdminBase
{
    use AddEditList;
    protected $basename;
    protected function initialize()
    {
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\".$this->basename."\\FuPart";
        $this->model = new $mdodel;
        $this->validate = "app\\common\\validate\\ArtPart";
        $this->validatename = ['add' => 'fuadd','edit' => 'fuedit'];
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->list_base['page'] = '1';
        $this->list_base['open'] = 'false';
        $this->Conf();
    }
    protected function getMap()
    {
        $map = [
            'status' => empty($this->getdata['status']) ? 'a' : $this->getdata['status']
        ];
        return array_merge(parent::getMap(),$map); // TODO: Change the autogenerated stub
    }

    protected function Conf(){
        if($this->contauth['create']) {
            $this->list_base['add'] = ['title' => '添加辅栏目', 'class' => 'cx-button-s cx-bg-green'];
        }
        $this->list_file = [['file' => 'id','title' => 'ID','type' => 'text','textalign' => 'center','width' => '80'],
            ['file' => 'title','title' => '辅栏目名称','type' => 'text','width' => '40%'],
            ['file' => 'article_num','title' => '内容数量','width' => '120','textalign' => 'center','type' => 'link','uri' => url($this->basename.'.fupartarticle/index',['fuid'=>'__id__'])->build(),],
            ['file' => 'sort','title' => '排序','type' => 'edit','textalign' => 'center','width' => '80',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']];
        if($this->request->action() == 'index'){
            $this->list_base['uri'] = url('getlist',empty($this->getdata['mid']) ? [] : ['mid' => $this->getdata['mid']])->build();
            if($this->contauth['create']) {
                $this->list_file[] = ['filed' => 'class','title' => '子栏目','type' => 'btn','text' => true,'open' => true,'opentitle' => "添加子栏目",'uri' => url('create',['pid' => '__id__'])->build(),'icon' => 'cx-iconadd cx-text-f16','class' => 'cx-text-black','textalign' => 'center','width' => '80'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑辅栏目",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
            }
        }else if(in_array($this->request->action(),['edit','create'])){
            $this->list_base['uri'] = url('save',$this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $usermodel = new UserGroup();
            $group_list = $usermodel->getList([]);
            $new_group_list = $get_group_list = [];
            if(!empty($group_list)){
                foreach ($group_list as $k => $v){
                    $new_group_list[$v['id']] = $v['title'];
                }
            }
            //  获取所有上级栏目
            $listdb = $this->model->getList();
            if(!empty($this->getdata['pid'])){
                $ids = NodeFormat::getChildsId($listdb,$this->getdata['pid']);
                foreach ($listdb as $k => $v){
                    if(in_array($v['id'],$ids)){
                        unset($listdb[$k]);
                    }
                    continue;
                }
            }
            if(!empty($this->getdata['id'])){
                $ids = NodeFormat::getChildsId($listdb,$this->getdata['id']);
                array_push($ids,$this->getdata['id']);
                foreach ($listdb as $k => $v){
                    if(in_array($v['id'],$ids)){
                        unset($listdb[$k]);
                    }
                    continue;
                }
            }
            $_listdb =[];
            if(!empty($listdb)){
                $listdb = NodeFormat::toList($listdb);
                foreach ($listdb as $k => $v){
                    if(!empty($this->getdata['id']) && $v['id'] == $this->getdata['id']){
                        continue;
                    }
                    $_listdb[$v['id']] = $v['title_display'];
                }
                $listdb = $_listdb;
                unset($_listdb);
            }
            $this->form_list = [
                ['file' => 'title','title' => '栏目名称','type' => 'text','required' => true],
                ['file' => 'pid','title' => '上级栏目','text_edit' => '顶级栏目','type' => 'select','data' => ['list' => $listdb,'default' => empty($this->getdata['pid']) ? '0' : (int) $this->getdata['pid']],'tip' => "如不选择则为顶级栏目"],
                ['file' => 'order','title' => '排序','type' => 'select','data' => ['list' => ['top desc,jian desc,addtime desc' => '默认排序'],'default' => '','default_edit' => '1'],'required' => true],
                ['file' => 'limit','title' => '列表显示','type' => 'text','type_edit' => 'number','type_unit' => '条','default' => '0','required' => true,'required_list' => 'number'],
                ['file' => 'title_num','title' => '内容标题显示','type' => 'text','type_edit' => 'number','type_unit' => '字','default' => '0','required' => true,'required_list' => 'number'],
                ['file' => 'cont_num','title' => '内容简介显示','type' => 'text','type_edit' => 'number','type_unit' => '字','default' => '0','required' => true,'required_list' => 'number'],
                ['file' => 'pid_see','title' => '上级栏目显示','type' => 'radio','data' =>['list' => ['0' => '隐藏','1' => '显示',],'default' => '1']],
                ['file' => 'group_see','title' => '允许查看','type' => 'checkbox','data' =>['list' => $new_group_list,'default' => '']],
                ['file' => 'group_edit','title' => '允许编辑','type' => 'checkbox','data' =>['list' => $new_group_list,'default' => '']],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' =>['list' => ['0' => '关闭栏目','1' => '启用栏目',],'default' => '1']],
                ['file' => 'sort','title' => '排序值','type' => 'text','required' => true,'default' => '0'],
                ['file' => 'jumpurl','title' => '跳转地址','type_group' => 'SEO及风格','type' => 'text','tip' => '要加http://或https://,如：http://www.cxbs.net'],
                ['file' => 'keywords','title' => 'SEO关键词','type_group' => 'SEO及风格','type' => 'text','tip' => '多个关键词请用英文’,‘进行分割，推荐80字以内',],
                ['file' => 'description','title' => 'SEO描述','type_group' => 'SEO及风格','type' => 'textarea','tip' => '推荐200字以内',],
                ['file' => 'banber','title' => 'BANBER','type_group' => 'SEO及风格','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'],
                ['file' => 'logo','title' => 'LOGO','type_group' => 'SEO及风格','type' => 'upload','upload_filenum' => '1','upload_autoup' => '1'],
                ['file' => 'password','title' => '栏目密码','type_group' => 'SEO及风格','type' => 'text','tip' => '设置密码后，本栏目所有内容均会加密'],
                ['file' => 'temp_late','title' => '自订义风格','type_group' => 'SEO及风格','type' => 'text'],
                ['file' => 'temp_head','title' => '自订义头部','type_group' => 'SEO及风格','type' => 'text','tip' => '独立头部风格，如：default/new_head.htm'],
                ['file' => 'temp_list','title' => '自订义列表','type_group' => 'SEO及风格','type' => 'text','tip' => '独立列表页风格，如：default/new_list.htm'],
                ['file' => 'temp_cont','title' => '自订义内容页','type_group' => 'SEO及风格','type' => 'text','tip' => '独立内容页风格，如：default/new_article.htm'],
                ['file' => 'temp_foot','title' => '自订义底部','type_group' => 'SEO及风格','type' => 'text','tip' => '独立底部风格，如：default/new_foot.htm'],
            ];
            if($this->request->action() == 'edit'){
                array_push($this->form_list,['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]);
            }
        }
    }
}