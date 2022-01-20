<?php
declare(strict_types = 1);

namespace app\common\model;

use think\Model;
class UserGroup extends Model {
    // 设置字段信息
    protected $schema = [
          "id" => "mediumint",
          "pid" => "mediumint",
          "title" => "string",
          "status" => "int",
          "rules" => "mediumtext",
          "mrules" => "mediumtext",
          "arules" => "mediumtext",
          "group_up" => "mediumint",
          "group_money" => "int",
          "group_icon" => "string",
          "group_type" => "tinyint",
          "group_admin" => "tinyint",
          "group_space" => "int",
          "sort" => "mediumint",
          "del_time" => "int",
    ];
    protected $type = [
        'rules'      =>  'array',
        'mrules'      =>  'array',
        'arules'      =>  'array',
    ];
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
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            $old = $this->whereId($data['id'])->find();
            if(!$old->save($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  获取用户组列表
    public function getList($data = []){
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['group_type']) ? $map->whereGroupType($data['group_type']) : $map;

        if(!empty($data['del_time'])){
            $map = $map->where('del_time','>','0');
        }else{
            $map = $map->where('del_time','0');
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        return $getlist;
    }

}