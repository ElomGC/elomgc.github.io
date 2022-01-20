<?php
declare(strict_types = 1);

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;

class Navlink extends ApiBase
{
    use AddEditList;
    protected function initialize(){
        parent::initialize();
    }
    public function index()
    {
        $getlist = $this->getdata['type'] == 'nav' ? $this->getNav() : $this->getLink();
        if(!empty($this->getdata['class'])){
            $getlist = empty($getlist[$this->getdata['class']]) ? [] : $getlist[$this->getdata['class']];
        }
        $this->result($getlist,'1','获取成功');
    }
}