<?php
declare(strict_types = 1);
namespace app\common\controller\home;

use app\common\controller\HomeBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use think\facade\View;
use worm\NodeFormat;

abstract class Article extends HomeBase
{
    use AddEditList;
    protected $basename;
    protected $PartModel;
    protected $ArtModel;
    protected $ArtFileModel;
    protected $Read;
    protected $Partdb;
    protected $menu;
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['1'] == 'home' ? $array['0']['3'] : $array['0']['1'];
        //  检测模块是否启用
        if(!$this->modelstatus($this->basename) || $this->webdb["{$this->basename}_status"] == '0'){
            $this->error("禁止访问，请联系管理员");
        }
        $mdodel = "app\\common\\model\\".$this->basename."\\Article";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\".$this->basename."\\Part";
        $this->PartModel = new $mdodel;
        $mdodel = "app\\common\\model\\".$this->basename."\\Artmodel";
        $this->ArtModel = new $mdodel;
        $mdodel = "app\\common\\model\\".$this->basename."\\Artmodelfile";
        $this->ArtFileModel = new $mdodel;
    }

    public function index()
    {
        $article = $this->model->whereId($this->getdata['id'])->find();
        $this->Read = $this->model->getOne($article['mid'],$article['id'],empty($this->wormuser['uid']) ? '' : $this->wormuser['uid']);
        if(empty($this->Read) || $this->Read['status'] != '1' || $this->Read['del_time'] > '0'){
            $this->error("内容不存在");
        }
        if(!empty($article['jumpurl'])){
            Db::name("{$this->basename}_content")->whereId($this->Read['id'])->update(['hist' => $this->Read['hist'] + 1]);
            Db::name("{$this->basename}_content_{$this->Read['mid']}")->whereId($this->Read['id'])->update(['hist' => $this->Read['hist'] + 1]);
            $this->redirect($article['jumpurl']);
        }
        $this->getPartList($this->Read['fid']);
        //  获取模板
        $this->list_temp = $this->tempview.$this->basename."/article_{$this->Read['mid']}.htm";
        foreach ($this->menu as $k => $v){
            if(!empty($v['temp_cont'])){
                $this->list_temp = $this->tempview.$this->basename."/{$v['temp_cont']}";
            }
        }
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        //  定义网站seo信息
        $this->webdb['web_title'] = "{$this->Read['title']}-{$this->Read['part_name']}-{$this->webdb['web_title']}";
        $this->webdb['web_keywords'] = empty($this->Partdb['keywords']) ? $this->webdb['web_keywords'] : $this->Partdb['keywords'];
        $this->webdb['web_keywords'] = empty($this->Read['keywords']) ? $this->webdb['web_keywords'] : $this->Read['keywords'];
        $this->webdb['web_description'] = empty($this->Partdb['description']) ? $this->webdb['web_description'] : $this->Partdb['description'];
        $this->webdb['web_description'] = empty($this->Read['description']) ? $this->webdb['web_description'] : $this->Read['description'];
        //  查询前后篇
        $article_page = $this->model->getUNpage($this->Read);
        Db::name("{$this->basename}_content")->whereId($this->Read['id'])->update(['hist' => $this->Read['hist'] + 1]);
        Db::name("{$this->basename}_content_{$this->Read['mid']}")->whereId($this->Read['id'])->update(['hist' => $this->Read['hist'] + 1]);
        View::assign(['menu' => $this->menu,'webdb' => $this->webdb,'partdb' => $this->Partdb,'partlist' => $this->PartList,'pnpage' => $article_page]);
        return $this->viewHomeRead(Upload::editadd($this->Read,false));
    }
    //  查询栏目
    protected function getPartList($fid){
        $getlist = $this->PartModel->getList();
        $getlist = Upload::editadd($getlist,false);
        $getlist = NodeFormat::toList($getlist);
        foreach ($getlist as $k => $v){
            $v['url'] = url('/'.$this->basename.'/part-'.$v['id'])->build();
            $getlist[$k] = $v;
        }
        $_getlist = Common::del_file($getlist,'id',$fid);
        if(empty($_getlist) || empty($_getlist['0']['status'])){
            $this->error("栏目不存在");
        }
        $_dgetlist = NodeFormat::toLayer($getlist,$_getlist['0']['id']);
        if(!empty($_dgetlist)){
            $_getlist['0']['node'] = $_dgetlist;
        }
        $this->menu = NodeFormat::getParents($getlist,$fid);
        //  获取面包屑
        $this->PartList = $getlist;
        $this->Partdb = $_getlist['0'];
        if(empty($this->Partdb['banber'])){
            foreach ($this->menu as $k => $v){
                $this->Partdb['banber'] = empty($v['banber']) ? $this->Partdb['banber'] : $v['banber'];
            }
        }
        return true;
    }
}