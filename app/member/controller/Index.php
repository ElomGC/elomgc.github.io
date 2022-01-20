<?php
declare(strict_types = 1);
namespace app\member\controller;

use app\common\controller\MemberBase;
use app\common\model\ModelList;
use app\common\wormview\AddEditList;

class Index extends MemberBase
{
    use AddEditList;
    public function index(){
        $this->list_temp = $this->hasview("index.htm");
        if(!is_file($this->list_temp)){
            $this->error("模板文件【{$this->list_temp}】不存在");
        }
        //  获取网站已有模块
        $_m = new ModelList();
        $_mlist = $_m->getList(['class' => '0','order' => 'id asc'],true);
        //  获取模块所有模型
        $_mlist = $this->getModellist($_mlist);
        return $this->viewMemberList($_mlist);
    }
    protected function getModellist($data){
        $_data = [];
        foreach ($data as $k => $v){
            $_m = "app\\common\\model\\{$v['keys']}\\Artmodel";
            $_m = new $_m;
            $_mlist = $_m->getList(['see_add' => '1']);
            foreach ($_mlist as $k1 => $v1){
                array_push($_data,[
                    'mid' => $v1['id'],
                    'm' => $v['keys'],
                    'title' => empty($v1['futitle']) ? $v1['title'] : $v1['futitle']
                ]);
            }
        }
        //  获取内容
        foreach ($_data as $k => $v){
            $_m = "app\\common\\model\\{$v['m']}\\Article";
            $_m = new $_m;
            $_mlist = $_m->getList(['mid' => $v['mid'],'uid' => $this->wormuser['uid'],'limit' => '10']);
            $_data[$k] = array_merge($v,$_mlist);
        }
        return $_data;
    }

}