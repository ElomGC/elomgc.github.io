<?php
declare(strict_types = 1);
namespace app\common\controller\home;

use app\common\controller\HomeBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\View;
use worm\NodeFormat;

abstract class Part extends HomeBase
{
    use AddEditList;
    protected $basename;
    protected $ArticleModel;
    protected $ArtModel;
    protected $Read;
    protected $Fids;
    protected $menu;
    protected $PartList;
    protected $fileModel;
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['1'] == 'home' ? $array['0']['3'] : $array['0']['1'];
        if(!$this->modelstatus($this->basename) || $this->webdb["{$this->basename}_status"] == '0'){
            $this->error("禁止访问，请联系管理员");
        }
        $mdodel = "app\\common\\model\\".$this->basename."\\Part";
        $ArticleModel = "app\\common\\model\\".$this->basename."\\Article";
        $fileModel = "app\\common\\model\\".$this->basename."\\Artmodelfile";
        $_mdodel = "app\\common\\model\\".$this->basename."\\Artmodel";
        $this->model = new $mdodel;
        $this->ArticleModel = new $ArticleModel;
        $this->fileModel = new $fileModel;
        $this->ArtModel = new $_mdodel;
        $this->getPartlist();
    }
    protected function getPartlist(){
        $_mids = $this->ArtModel->getList(['status' => '1']);
        $_mids = array_column($_mids,'id');
        $getlist = $this->model->getList(['mid' => $_mids]);
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
        $_getlist['0']['pids'] = NodeFormat::getChildsId($getlist,$this->menu['0']['id']);
        array_push($_getlist['0']['pids'],$this->menu['0']['id']);
        //  获取面包屑
        foreach ($getlist as $k => $v){
            unset($v['status'],$v['limit'],$v['title_num'],$v['cont_num'],$v['group_uid'],$v['group_see'],$v['group_edit'],$v['pid_see'],$v['comment_see'],$v['jumpurl'],$v['password'],$v['temp_late'],$v['temp_head'],$v['temp_list'],$v['temp_cont'],$v['temp_foot'],$v['order'],$v['article_num']);
            $v['url'] = url('/'.$this->basename.'/part-'.$v['id'])->build();
            $getlist[$k] = $v;
        }
        $this->PartList = $getlist;
        $this->Read = $_getlist['0'];
        foreach ($this->menu as $k => $v){
            $v['url'] = url('/'.$this->basename.'/part-'.$v['id'])->build();
            $this->menu[$k] = $v;
            if(empty($this->Read['banber'])){
                $this->Read['banber'] = empty($v['banber']) ? $this->Read['banber'] : $v['banber'];
            }
        }
        if(!empty($this->getdata['order'])){
            switch ($this->getdata['order']){
                case '1':
                    $this->Read['order'] = 'id desc';
                    break;
                case '2':
                    $this->Read['order'] = 'hist desc';
                    break;
            }
        }
    }
    public function index()
    {
        if(!empty($this->Read['jumpurl'])){
            $this->redirect($this->Read['jumpurl']);
        }
        if(!empty($this->Read['password']) && session("seepart{$this->Read['id']}") != '1'){
            $this->redirect(url('seepart',['id' => $this->getdata['id']]));
        }
        //  获取模板
        $this->list_temp = $this->hasview("{$this->basename}/list_{$this->Read['mid']}.htm");
        $this->list_temp = !empty($this->Read['temp_list']) && is_file(root_path($this->tempview).$this->Read['temp_list']) ? $this->tempview.$this->Read['temp_list'] : $this->list_temp;
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        if(!empty($this->Read['temp_head']) && is_file(root_path($this->tempview).$this->Read['temp_head'])){
            View::assign(['head' => $this->tempview.$this->Read['temp_head']]);
        }
        if(!empty($this->Read['temp_foot']) && is_file(root_path($this->tempview).$this->Read['temp_foot'])){
            View::assign(['foot' => $this->tempview.$this->Read['temp_foot']]);
        }
        if($this->Read['class'] == '1') {
            $this->list_temp = $this->hasview("single.htm");
            $this->list_temp = !empty($this->Read['temp_list']) && is_file(root_path($this->tempview).$this->Read['temp_list']) ? $this->tempview.$this->Read['temp_list'] : $this->list_temp;
            if(!is_file($this->list_temp)){
                $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
            }
            $this->Read['content'] = $this->Read['description'];
            $this->Read['description'] = str_replace(PHP_EOL, '', $this->Read['description']);
            $this->Read['description'] = preg_replace('/<([^<]*)>/is',"",$this->Read['description']);
            $this->Read['description'] = preg_replace('/ |　|&nbsp;/is',"",$this->Read['description']);	//把多余的空格去除掉
            $this->Read['description'] = preg_replace('/\s/is',"",$this->Read['description']);
            $this->Read['description'] = get_word($this->Read['description'],300,false);	//把多余的空格去除掉
            $articlelist = [];
        } else {
            //  获取文章
            $articlelist = $this->ArticleModel->getList(['fid' => $this->Fids]);
        }
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
            $this->redirect("/home/part-{$this->getdata['id']}.html");
        }
        $this->list_temp = $this->tempview."seepassword.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewHomeList();
    }
}