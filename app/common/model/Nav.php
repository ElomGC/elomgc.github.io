<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\wormview\Upload;
use think\facade\Cache;
use think\Model;
use worm\NodeFormat;

class Nav extends R {

    public function getList($data = []){
        $data['status'] = !isset($data['status']) ? '1' : $data['status'];
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        if(!empty($data['class'])){
            $map = $map->whereClass($data['class']);
        }
        if(!empty($data['pid'])){
            $map = $map->wherePid($data['pid']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        return $getlist;
    }
    public function getOne($id)
    {
        $data = $this->getList(['id' => $id,'status' => 'a']);
        return empty($data['0']) ? [] : $data['0'];
    }
    //  格式化栏目列表
    public function getHomeList($data = []){
        $getlist = $this->getList($data);
        $getlist = Upload::editadd($getlist,false);
        $getlist = NodeFormat::toList($getlist);
        return $getlist;
    }
    public function DeleteOne($id){
        $ids = $this->wherePid($id)->column('id');
        array_push($ids,$id);
        if($this->whereIn('id',$ids)->delete()){
            return true;
        }
        return false;
    }
    public function setOne($data)
    {
        Cache::delete('NavList');
        return parent::setOne($data);
    }
}