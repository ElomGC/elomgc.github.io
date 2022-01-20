<?php
declare(strict_types = 1);
namespace app\cms\controller;

use app\common\controller\HomeBase;
use app\common\wormview\Label;

class Index extends HomeBase {
    use Label;
    public function index(){
        $this->list_temp = $this->hasview("bbs/index.htm");
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewWeb($this->list_temp);
    }

}