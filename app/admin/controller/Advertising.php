<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class Advertising extends AdminBase
{
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\Advertising";
        $this->validate = "app\\common\\validate\\Advertising";
        $this->model = new $models;
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        if($this->request->action() == 'index'){
            $this->list_base['uri'] = url('getlist', !empty($this->getdata['class']) && $this->getdata['class'] != 'a' ? ['class' => $this->getdata['class']] : []);
            if ($this->contauth['create']) {
                $this->getdata['class'] = empty($this->getdata['class']) ? 'a' : $this->getdata['class'];
                $this->list_top = [];
                if(in_array($this->getdata['class'],['a','1'])){
                    array_push($this->list_top,['title' => '添加图片广告', 'class' => 'cx-button-s cx-bg-green cx-mag-r10','uri' => url('create',['class' => '1'])->build()]);
                }
                if(in_array($this->getdata['class'],['a','2'])){
                    array_push($this->list_top,['title' => '添加幻灯广告', 'class' => 'cx-button-s cx-bg-green cx-mag-r10','uri' => url('create',['class' => '2'])->build()]);
                }
                if(in_array($this->getdata['class'],['a','3'])){
                    array_push($this->list_top,['title' => '添加图文广告', 'class' => 'cx-button-s cx-bg-green cx-mag-r10','uri' => url('create',['class' => '3'])->build()]);
                }
                if(in_array($this->getdata['class'],['a','4'])){
                    array_push($this->list_top,['title' => '添加文字广告', 'class' => 'cx-button-s cx-bg-green','uri' => url('create',['class' => '4'])->build()]);
                }
            }
            $this->list_nav = [
                'list' => [
                    ['id' => 'a','title' => '全部','uri' => url('index',['class' => 'a'])],
                    ['id' => '1','title' => '图片广告','uri' => url('index',['class' => '1'])],
                    ['id' => '2','title' => '幻灯广告','uri' => url('index',['class' => '2'])],
                    ['id' => '3','title' => '图文广告','uri' => url('index',['class' => '3'])],
                    ['id' => '4','title' => '文字广告','uri' => url('index',['class' => '4'])],
                ],
                'default' => empty($this->getdata['class']) ? 'a' : $this->getdata['class'],
            ];
            $this->list_file = [
                ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center','fixed' => 'left'],
                ['file' => 'title', 'title' => '广告名称', 'type' => 'text','width' => '50%'],
                ['file' => 'class', 'title' => '广告类型','type' => 'radio','data' => ['1' => '图片广告','2' => '幻灯广告','3' => '图文广告','4' => '文字广告'],'textalign' => 'center','width' => '12%'],
                ['file' => 'addtime','title' => '添加时间','type' => 'text','textalign' => 'center','width' => '180'],
                ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
            ];
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑广告",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80','fixed' => 'right'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','fixed' => 'right'];
            }
        }elseif(in_array($this->request->action(),['create','edit','save'])) {
            if(in_array($this->request->action(),['create','edit'])){
                $this->list_base['uri'] = url('save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            }
            $this->list_temp = 'add';
        }
    }

}