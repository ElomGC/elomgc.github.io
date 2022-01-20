<?php
declare(strict_types = 1);

namespace app\api\controller;


use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;

class Useraddress extends ApiBase
{
    use AddEditList;
    protected function initialize()
    {
        parent::initialize();
        if(!$this->isLogin()){
            $this->result('','10000','请登录');
        }
        $models = "app\\common\\model\\UserAddress";
        $this->validate = "app\\common\\validate\\User";
        $this->validatename = [
            'add' => 'address',
            'edit' => 'editress',
        ];
        $this->model = new $models;
    }
    protected function getMap()
    {
        $map = [
            'uid' => $this->wormuser['uid'],
            'limit' => empty($this->getdata['limit']) ? '' : $this->getdata['limit'],
            'page' => empty($this->getdata['page']) ? '' : $this->getdata['page'],
        ];
        return $map;
    }
    public function read($id)
    {
        $getdata = $this->model->getOne($id);
        if(!$getdata){
            $this->result('','0','地址不存在');
        }
        if($getdata['uid'] != $this->wormuser['uid']){
            $this->result('','0','非法访问');
        }
        return $this->viewApiRead($getdata);
    }
}