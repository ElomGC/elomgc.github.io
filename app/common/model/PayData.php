<?php
declare(strict_types = 1);

namespace app\common\model;


class PayData extends R
{
    protected $schema = [
        "id" => 'int',
        "oid" => 'varchar',
        "model" => 'varchar',
        "mid" => 'int',
        "aid" => 'int',
        "title" => 'varchar',
        "picurl" => 'varchar',
        "admincont" => 'text',
        "money" => 'int',
        "money_zk" => 'int',
        "num" => 'int',
        "money_zon" => 'int',
        "fu_cont" => 'text',
    ];
    //  保存订单
    public function setList($data,$oid = ''){
        if(!$this->saveAll($data)){
            return false;
        }
        if(!empty($oid)) {
            $oldlist = $this->whereOid($oid)->select()->toArray();
        }else{
            $ids = array_column($data,'id');
            $oldlist = $this->whereIn('id',$ids)->select()->toArray();
        }
        return $oldlist;
    }

}