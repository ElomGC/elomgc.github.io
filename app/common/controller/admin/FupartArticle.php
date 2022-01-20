<?php
declare(strict_types = 1);

namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class FupartArticle extends AdminBase
{
    use AddEditList;
    protected function initialize()
    {
        parent::initialize();
        if(empty($this->getdata['fuid'])){
            $this->error("非法访问");
        }
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $models = "app\\common\\model\\{$this->basename}\\FuPartArticle";
        $this->model = new $models;
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
        }
        array_push($this->list_file,['file' => 'id', 'title' => 'ID', 'type' => 'text', 'width' => '80', 'textalign' => 'center', 'fixed' => 'left'],
            ['file' => 'fupart_name', 'title' => '辅栏目', 'type' => 'text','textalign' => 'center','width' => '180'],
            ['file' => 'title', 'title' => '标题', 'type' => 'link','target' => '_blank','uri' => url("/home/{$this->basename}/article/index",['id'=>'__aid__'])->build(),'width' => '40%'],
            ['file' => 'part_name', 'title' => '所属栏目', 'type' => 'link','target' => '_blank','uri' => url($this->basename.".article/index",['fid'=>'__fid__'])->build(),'textalign' => 'center','width' => '180'],
            ['file' => 'addtime', 'title' => '发表时间', 'type' => 'text', 'textalign' => 'center','width' => '180']
        );
        if($this->request->action() == 'index'){
            $this->list_base['uri'] =  url('getlist',['fuid' => $this->getdata['fuid']])->build();
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right','uri' => url('del',['fuid' => $this->getdata['fuid']])->build()];
                $this->list_top = [
                    ['title' => '返回辅栏目列表', 'class' => 'cx-button-s cx-bg-green cx-mag-r10', 'uri' => url($this->basename.'.fupart/index')->build()],
                    ['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                ];
            }
        }
    }
}