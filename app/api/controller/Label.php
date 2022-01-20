<?php
declare(strict_types = 1);

namespace app\api\controller;

use app\common\controller\HomeBase;
use think\facade\Cookie;

class Label extends HomeBase {

    public function webor(){
        if(Cookie::get("label")){
            Cookie::delete("label");
            $this->success("退出标签管理");
        }else{
            Cookie::set("label",'editvalue');
            $this->success("进入标签管理");
        }
    }
}