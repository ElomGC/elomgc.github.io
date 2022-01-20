<?php
declare(strict_types = 1);

namespace app\common\controller\home;


use app\common\controller\HomeBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\View;
use worm\NodeFormat;

class Fupart extends HomeBase
{
    use AddEditList;
    protected $basename;
    protected $articleModel;
    protected $PartList;
    protected $menu;
    protected $Read;
    protected $Fids;
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['1'] == 'home' ? $array['0']['3'] : $array['0']['1'];
        if (!$this->modelstatus($this->basename) || $this->webdb["{$this->basename}_status"] == '0') {
            $this->error("禁止访问，请联系管理员");
        }
        $_model = "app\\common\\model\\".$this->basename."\\FuPart";
        $this->model = new $_model;
        $_model = "app\\common\\model\\".$this->basename."\\FuPartArticle";
        $this->articleModel = new $_model;
        $this->getPartlist();
    }
    protected function getPartlist(){
        $getlist = $this->model->getList();
        $_read = Common::del_file($getlist,'id',$this->getdata['id']);
        $_read = empty($_read['0']) ? [] : $_read['0'];
        if(empty($_read) || $_read['status'] != '1'){
            $this->error("栏目不存在");
        }
        $getlist = Upload::editadd($getlist,false);
        $getlist = NodeFormat::toList($getlist);
        $this->Fids = NodeFormat::getChildsId($getlist,$this->getdata['id']);
        array_push($this->Fids,$this->getdata['id']);
        $_getlist = Common::del_file($getlist,'id',$this->getdata['id']);
        $_dgetlist = NodeFormat::toLayer($getlist,$_getlist['0']['id']);
        if(!empty($_dgetlist)){
            $_getlist['0']['node'] = $_dgetlist;
        }
        $this->menu = NodeFormat::getParents($getlist,$this->getdata['id']);
        //  获取面包屑
        $this->PartList = $getlist;
        $this->Read = $_getlist['0'];
    }
    public function index()
    {
        if (!empty($this->Read['jumpurl'])) {
            $this->redirect($this->Read['jumpurl']);
        }
        if (!empty($this->Read['password']) && session("seepart{$this->Read['id']}") != '1') {
            $this->redirect(url('seepart', ['id' => $this->getdata['id']]));
        }
        //  获取模板
        $this->list_temp = $this->tempview.$this->basename."/fu_list.htm";
        $this->list_temp = !empty($this->Read['temp_list']) && is_file(root_path().$this->tempview.$this->Read['temp_list']) ? $this->tempview.$this->Read['temp_list'] : $this->tempview.$this->basename."/fu_list.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        if(!empty($this->Read['temp_head']) && is_file(root_path().$this->tempview.$this->Read['temp_head'])){
            View::assign(['head' => $this->tempview.$this->Read['temp_head']]);
        }
        if(!empty($this->Read['temp_foot']) && is_file(root_path().$this->tempview.$this->Read['temp_foot'])){
            View::assign(['foot' => $this->tempview.$this->Read['temp_foot']]);
        }
        $articlelist = $this->articleModel->getList(array_merge($this->getMap(),['fuid' => $this->Fids]));
        //  定义网站seo信息
        $this->webdb['web_title'] = "{$this->Read['title']}-{$this->webdb['web_title']}";
        $this->webdb['web_keywords'] = empty($this->Read['keywords']) ? $this->webdb['web_keywords'] : $this->Read['keywords'];
        $this->webdb['web_description'] = empty($this->Read['description']) ? $this->webdb['web_description'] : $this->Read['description'];
        View::assign(['menu' => $this->menu,'webdb' => $this->webdb,'readdb' => $this->Read,'partlist' => $this->PartList]);
        return $this->viewHomeList(Upload::editadd($articlelist,false));
    }
    public function seepart(){
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            if(empty($data['pwd']) || $data['pwd'] != $this->Read['password']){
                $this->error("密码不正确");
            }
            session("seepart{$this->Read['id']}",'1');
            $this->redirect("/home/fupart-{$this->getdata['id']}.html");
        }
        $this->list_temp = $this->tempview."seepassword.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewHomeList();
    }
}