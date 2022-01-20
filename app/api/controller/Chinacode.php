<?php
declare(strict_types = 1);

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;

class Chinacode extends ApiBase
{
    use AddEditList;
    protected function initialize(){
        parent::initialize();
        $mdodel = "app\\common\\model\\Chinacode";
        $this->model = new $mdodel;
    }
    protected function getMap()
    {
        $map = [
            'zoneid' => empty($this->getdata['zoneid']) ? '' : $this->getdata['zoneid'],
            'parzoneid' => empty($this->getdata['parzoneid']) ? '' : $this->getdata['parzoneid'],
        ];
        return $map;
    }

}