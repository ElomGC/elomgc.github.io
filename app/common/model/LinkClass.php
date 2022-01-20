<?php
declare(strict_types = 1);

namespace app\common\model;

use think\Model;

class LinkClass extends Model {

    public function getList($data = []){
        $data['status'] = !isset($data['status']) ? '1' : $data['status'];
        $data['del_time'] = empty($data['del_time']) ? '0' : $data['del_time'];
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereId($data['id']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if(empty($data['del_time'])){
            $map = $map->whereDelTime('0');
        }else{
            $map = $map->where('del_time','>','0');
        }
        $getlist = $map->order('id asc')->select()->toArray();
        return $getlist;
    }

}