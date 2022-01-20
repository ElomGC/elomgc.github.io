<?php
declare(strict_types = 1);

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class Paybase extends AdminBase
{
    use AddEditList;
    protected $PayDataModel;
    protected $PayLinshiModel;
    protected function initialize()
    {
        parent::initialize();
        $mdodel = "app\\common\\model\\PayBase";
        $PayDataModel = "app\\common\\model\\PayData";
        $PayLinshiModel = "app\\common\\model\\PayLinshi";
        $this->model = new $mdodel;
        $this->PayDataModel = new $PayDataModel;
        $this->PayLinshiModel = new $PayLinshiModel;
        $this->validate = "app\\common\\validate\\PayBase";
        $this->list_base['id'] = "oid";
        $this->getConf();
    }
    protected function getConf(){
        $this->list_base['title'] = "导航";
        $this->list_file = [
            ['file' => 'title_display', 'title' => '导航名称', 'type' => 'text',],
            ['file' => 'status', 'title' => '状态','id_edit' => 'oid', 'type' => 'switch', 'class' => 'cx-text-center', 'width' => '80', 'switch' => ['0' => ['value' => "等待付款"], '1' => ['value' => "已付款"]]]
        ];
        $this->list_rightbtn = [
            ['type' => 'edit','id_edit' => 'oid'],
            ['type' => 'del','id_edit' => 'oid'],
        ];
        if($this->request->action() == 'index'){
            $this->list_temp = 'index';
        }else if($this->request->action() == 'edit'){
            $this->list_temp = 'read';
        }

    }
}