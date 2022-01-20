<?php
declare(strict_types = 1);

namespace app\common\controller;

use app\facade\hook\Common;
use think\facade\Cache;
use think\facade\Session;
use think\facade\View;

class AdminBase extends Base {
    protected $model;
    protected $validate;
    protected $list_base = [
        'page' => '0',
    ];
    protected $list_file = [];
    protected $list_nav;
    protected $list_top;
    protected $form_list;
    protected $list_search;
    protected $contauth;
    protected $allauth = false;
    protected function initialize()
    {
        parent::initialize();
        if(!Session::has('_admin_') && !$this->allauth){
            $this->redirect(url('login/login')->build());
        }
        $this->index_list = false;
        $this->tempview();
        $this->CheckAuth();
        $this->list_base['uri'] = url('getlist')->build();
    }
    //  获取权限
    protected function WormAuth(){
        if($this->allauth){
            $this->wormuser = getUser('1');
            $this->wormuser['open'] = '1';
            View::assign(['wormuser' => $this->wormuser]);
            event('authrule',$this->wormuser);
        }else if(!$this->allauth && !Cache::has('userAuth_'.$this->wormuser['uid'])){
            Cache::delete('userAuth_'.$this->wormuser['uid']);
            event('authrule',$this->wormuser);
        }
        return Cache::get('userAuth_'.$this->wormuser['uid']);
    }
    //  验证权限
    protected function CheckAuth($data = []){
        $userauth = $this->WormAuth();
        $this->userauth = $userauth['1']['c'];
        $authlist = array_column($this->userauth,'name');
        $_authlist = array_column($userauth['1']['n'],'name');
        array_push($authlist,'index/index','login/login','login/qulogin');
        if(empty($data)){
            $auth = "{$this->request->controller()}/{$this->request->action()}";
            if(!in_array($auth,$authlist) && in_array($auth,$_authlist)){
                $this->error("你没有此权限，请联系管理员1");
            }
            $_auth = Common::del_file($this->userauth,'name',$auth);
            $_auth = Common::del_null(array_unique(array_column($_auth,'condition')));
            if(!empty($_auth)){
                $cv = false;
                foreach ($_auth as $k => $v){
                    if(empty($v)){
                        continue;
                    }
                    $_v = explode(',',$v);
                    $_cv = [];
                    foreach ($_v as $k1 => $v1){
                        $v1 = explode('=',$v1);
                        $_cv[$v1['0']] = $v1['1'];
                    }
                    if(empty(array_diff($_cv,$this->getdata))){
                        $cv = true;
                    }
                    continue;
                }
                if(!$cv){
                    $this->error("你没有此权限，请联系管理员");
                }
            }
        }else{
            $data = is_array($data) ? $data : explode(',',$data);
            $_data = [];
            foreach ($data as $k => $v){
                $auth = "{$this->request->controller()}/{$v}";
                $_data[$v] = true;
                if(!in_array($auth,$authlist) && in_array($auth,$_authlist)){
                    $_data[$v] = false;
                }
            }
            return $_data;
        }
    }
}