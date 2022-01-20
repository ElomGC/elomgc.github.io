<?php
declare(strict_types = 1);
namespace app\common\controller;

use app\common\model\ModelList;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\View;

class HomeBase extends Base {
    protected $LABEL;
    protected $basedir;
    protected $model;
    protected $list_temp;
    protected function initialize(){
        parent::initialize();
        //  判断是否登录过后台
        $webonclick = '';
        if(session('_admin_') && $this->wormuser['group_type'] == '0' &&  $this->wormuser['group_admin'] == '1'){
            $webonclick = "class='cx-bodydbclick'";
            $this->LABEL = Cookie::get("label") ? true : false;
        }
        //  判断网站是否开启
        if($this->webdb['web_open'] == '0'){
            $this->error(empty($this->webdb['web_openwhy']) ? '网站已关闭，请稍后访问' : $this->webdb['web_openwhy']);
        }
        $this->tempview($this->webdb['web_template']);
        View::assign(['webonclick'=>$webonclick,'NavList' => $this->getNav(),'LinkList' => $this->getLink(),'LABEL' => $this->LABEL]);
    }
    protected function modelstatus($keys){
        $mdodel = new ModelList();
        $res = $mdodel->getList(['keys' => $keys,'status' => 'a','page' => '1']);
        $res = $res['total'] < '1' ? [] : $res['data']['0'];
        if(empty($res) || empty($res['status'])){
            return false;
        }
        return true;
    }
}