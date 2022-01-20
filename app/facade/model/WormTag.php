<?php
declare(strict_types = 1);
namespace app\facade\model;
use think\Facade;
class WormTag extends Facade {
    protected static function getFacadeClass(){
         return 'app\common\model\WormTag';
    }
}