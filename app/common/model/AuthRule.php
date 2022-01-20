<?php
declare(strict_types = 1);
namespace app\common\model;

use app\facade\hook\Common;use think\Model;

class AuthRule extends Model{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'pid'          => 'int',
        'title'        => 'string',
        'name'        => 'string',
        'icon'        => 'string',
        'condition'        => 'string',
        'status'      => 'tinyint',
        'open'      => 'tinyint',
        'type_class'      => 'mediumint',
        'menusee'      => 'tinyint',
        'topsee'      => 'tinyint',
        'sort'      => 'mediumint',
    ];
    //  获取权限列表
    public function getList($data = []){
        $map = $this;
        $map = !empty($data['type_class']) ? $map->whereIn('type_class',is_array($data['type_class']) ? $data['type_class'] : (string) $data['type_class']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = empty($data['open']) ? $map->whereOpen('0') : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        $getlist = Common::JobArray($getlist);
        return $getlist;
    }
    //  验证规则地址
    public function SceneAuth($data){
        $old = $this->whereName($data['name'])->select();
        if($old->isEmpty()){
            return true;
        }
        $old = Common::JobArray($old);
        $res = true;
        $data['id'] = empty($data['id']) ? '0' : $data['id'];
        foreach ($old as $k => $v){
            if($v['type'] == $data['type'] && $data['condition'] == $v['condition'] && $v['id'] != $data['id']){
               $res = false;
               break;
            }
            continue;
        }
        return $res;
    }
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
        $_data['pid'] = empty($_data['pid']) ? '0' : $_data['pid'];
        if($_data['pid'] != '0'){
            $_data['type_class'] = $this->whereId($_data['pid'])->value('type_class');
        }
        return $data;
    }
    //  保存数据
    public function setOne($data){
        $data = $this->getEditAdd($data);
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  获取模块权限
    public function getModelconf($data){
        $_data = [];
        foreach ($data as $k => $v){
            $_data = array_merge($_data,explode(',',$v));
        }
        $authlist = $this->whereIn('id',$_data)->column('name');
        $_authlist = [];
        foreach ($authlist as $k => $v){
            $v = explode('.',$v);
            if(count($v) < 2){
                continue;
            }
            $_authlist[] = $v['0'];
        }
        return array_unique($_authlist);
    }
}