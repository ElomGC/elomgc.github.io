<?php
declare(strict_types = 1);

namespace hook;

use app\common\wormview\Tag as cxModel;

class Tag
{
    use cxModel;
    public function GetArticleList($data){
        return $this->TagAritclelist($data);
    }
    public function GetPartList($data){
        return $this->TagPartlist($data);
    }
    public function GetNavList($data){
        return $this->TagGetNavList($data);
    }
    public function GetAdvertising($data){
        return $this->TagAdvertising($data);
    }
}