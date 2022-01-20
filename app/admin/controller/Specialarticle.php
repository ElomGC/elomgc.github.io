<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class Specialarticle extends AdminBase
{
    use AddEditList;
    protected function initialize()
    {
        parent::initialize();
        if(empty($this->getdata['sid'])){
            $this->error("非法访问");
        }
        $models = "app\\common\\model\\SpecialArticle";
        $this->model = new $models;
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
        }
        array_push($this->list_file,['file' => 'id', 'title' => 'ID', 'type' => 'text', 'width' => '80', 'textalign' => 'center', 'fixed' => 'left'],
            ['file' => 'special_name', 'title' => '专题', 'type' => 'text','textalign' => 'center','width' => '180'],
            ['file' => 'title', 'title' => '标题', 'type' => 'link','target' => '_blank','uri' => url('/home/__model__/article/index',['id'=>'__aid__'])->build(),'width' => '40%'],
            ['file' => 'part_name', 'title' => '所属栏目', 'type' => 'text','textalign' => 'center','width' => '180'],
            ['file' => 'addtime', 'title' => '发表时间', 'type' => 'text', 'textalign' => 'center','width' => '180']
        );
        if($this->request->action() == 'index'){
            $this->list_base['uri'] =  url('getlist',['sid' => $this->getdata['sid']])->build();
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right','uri' => url('del',['sid' => $this->getdata['sid'],'id' => '__id__'])->build()];
                $this->list_top = [
                    ['title' => '返回专题列表', 'class' => 'cx-button-s cx-bg-green cx-mag-r10', 'uri' => url('special/index')->build()],
                    ['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                ];
            }
        }
    }
}