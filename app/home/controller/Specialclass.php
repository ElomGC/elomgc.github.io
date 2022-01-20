<?php
declare(strict_types = 1);

namespace app\home\controller;

use app\common\controller\HomeBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\View;
use worm\NodeFormat;

class Specialclass extends HomeBase
{
    use AddEditList;
    protected $SpecialModel;
    protected $Read;
    protected $PartList;
    protected $menu;
    protected function initialize(){
        parent::initialize();
        $mdodel = "app\\common\\model\\SpecialClassBase";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\Special";
        $this->SpecialModel = new $mdodel;
        $PartList = $this->model->getList();
        $this->PartList = NodeFormat::toList($PartList);
        $this->Read = empty($this->getdata['id']) ? [] : Common::del_file($PartList,'id',$this->getdata['id']);
        $this->Read = empty($this->Read) ? ['id' => '0','title' => '专题列表'] : $this->Read['0'];
        $this->menu = empty($this->getdata['id']) ? [['id' => '0','title' => '专题列表']] : NodeFormat::getParents($PartList,$this->getdata['id']);
    }
    public function index()
    {
        $this->list_temp = $this->tempview."special_list.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        $article = $this->SpecialModel->getList(['cid' => empty($this->getdata['id']) ? array_column($this->PartList,'id') : $this->getdata['id']]);
        $article = Upload::editadd($article,false);
        //  定义网站seo信息
        $this->webdb['web_title'] = "专题列表-{$this->webdb['web_title']}";
        View::assign(['menu' => $this->menu,'webdb' => $this->webdb,'readdb' => $this->Read,'partlist' => $this->PartList]);
        return $this->viewHomeList($article);
    }
}