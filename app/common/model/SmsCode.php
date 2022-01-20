<?php
declare(strict_types = 1);

namespace app\common\model;

use think\Model;

class SmsCode extends R {

    //  清洗数据
    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        return $_data;
    }
    //  保存数据
    public function setOne($data){
        if(!$add = $this->create($data)){
            return false;
        }
        $res = $this->whereId($add->id)->find()->toArray();
        return $res;
    }

}