<?php
declare(strict_types = 1);

namespace app\home\controller;

use app\common\controller\HomeBase;
use app\common\wormview\AddEditList;
use app\facade\wormview\Upload;
use think\facade\View;
use worm\NodeFormat;

class Special extends HomeBase
{
    use AddEditList;
    protected $ClassModel;
    protected $ClassList;
    protected $ArticleModel;
    protected $Read;
    protected $menu;
    protected function initialize()
    {
        parent::initialize();
        if(empty($this->getdata['id'])){
            $this->error("非法访问");
        }
        $mdodel = "app\\common\\model\\Special";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\SpecialClassBase";
        $this->ClassModel = new $mdodel;
        $mdodel = "app\\common\\model\\SpecialArticle";
        $this->ArticleModel = new $mdodel;
        $PartList = $this->ClassModel->getList();
        $this->ClassList = NodeFormat::toList($PartList);
        $this->Read = $this->model->getOne($this->getdata['id']);
        if(empty($this->Read)){
            $this->error("专题不存在");
        }
        $this->Read = Upload::editadd($this->Read,false);
        $this->menu = NodeFormat::getParents($PartList,$this->Read['cid']);
    }
    public function index()
    {
        $this->list_temp = !empty($this->Read['temp_list']) && is_file(root_path().$this->tempview.$this->Read['temp_list']) ? $this->tempview.$this->Read['temp_list'] : $this->tempview."/special.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        if(!empty($this->Read['temp_head']) && is_file(root_path().$this->tempview.$this->Read['temp_head'])){
            View::assign(['head' => $this->tempview.$this->Read['temp_head']]);
        }
        if(!empty($this->Read['temp_foot']) && is_file(root_path().$this->tempview.$this->Read['temp_foot'])){
            View::assign(['foot' => $this->tempview.$this->Read['temp_foot']]);
        }
        $article = $this->ArticleModel->getList(['sid' => $this->Read['id'],'page' => empty($this->getdata['page']) ? '1' : $this->getdata['page']]);
        //  定义网站seo信息
        $this->webdb['web_title'] = "{$this->Read['title']}-{$this->webdb['web_title']}";
        $this->webdb['web_keywords'] = empty($this->Read['keywords']) ? $this->webdb['web_keywords'] : $this->Read['keywords'];
        $this->webdb['web_description'] = empty($this->Read['description']) ? $this->webdb['web_description'] : $this->Read['description'];
        View::assign(['menu' => $this->menu,'webdb' => $this->webdb,'readdb' => $this->Read,'partlist' => $this->ClassList]);
        return $this->viewHomeList($article);
    }
}