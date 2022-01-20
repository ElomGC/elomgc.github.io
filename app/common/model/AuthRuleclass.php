<?php
declare(strict_types = 1);
namespace app\common\model;

use think\Model;

class AuthRuleclass extends Model {

    //  获取权限分类
    public function getList($data = []){
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereIn('id',$data['id']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->select()->toArray();
        foreach ($getlist as $k => $v){
            $getlist[$k] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'uri' => empty($v['uri']) ? url('authrule/index',array('t' => $v['id']))->build() : $v['uri'],
            ];
        }
        return $getlist;
    }

}