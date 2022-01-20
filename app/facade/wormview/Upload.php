<?php
declare(strict_types = 1);
namespace app\facade\wormview;
use think\Facade;
class Upload extends Facade {
    protected static function getFacadeClass(){
         return 'app\common\wormview\Upload';
    }
}