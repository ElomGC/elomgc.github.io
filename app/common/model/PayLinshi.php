<?php
declare(strict_types = 1);

namespace app\common\model;


class PayLinshi extends R
{
    public function setOne($data,$files = 'lsoid'){
        //  查询原有数据
        $old = $this->where($files,$data[$files])->find();
        if(empty($old)){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->where($files,$data[$files])->update($data)){
                return false;
            }
        }
        $old = $this->where($files,$data[$files])->find();
        if(!empty($old['pay_no'])){
            $this->whereOid($old['oid'])->where('lsoid','<>',$old['lsoid'])->delete();
        }
        return $old;
    }
}