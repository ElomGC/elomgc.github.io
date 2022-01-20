<?php
declare (strict_types = 1);
namespace app\facade\hook;

use think\Facade;

class Common extends Facade {
    protected static function getFacadeClass(){
        return 'hook\Common';
    }
}