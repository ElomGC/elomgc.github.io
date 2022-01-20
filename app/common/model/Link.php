<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\Model;

class Link extends Model {

    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v) {
            if (isset($data[$v])) {
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = empty($data['id']) ? [] : $this->whereId($_data['id'])->find()->toArray();
        if(empty($old)){
            unset($_data['id']);
        }
        $_data['logo'] = Common::setFile(empty($_data['logo']) ? null : $_data['logo'],empty($old['logo']) ? null : Upload::editadd($old['logo'],false));
        $_data['logo'] = empty($_data['logo']) ? null : Upload::editadd($_data['logo']);
        return $_data;
    }
    public function getList($data = []){
        $data['status'] = !isset($data['status']) ? '1' : $data['status'];
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereIn('id',$data['id']);
        }
        if(!empty($data['class'])){
            $map = $map->whereIn('class',$data['class']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        return $getlist;
    }

    public function DeleteOne($id){
        $ids = $this->getList(['id' => $id]);
        $ids = Upload::editadd($ids,false);
        $img = Common::del_null(array_column($ids,'logo'));
        $ids = array_column($ids,'id');
        if($this->whereIn('id',$ids)->delete()){
            Upload::fileDel($img);
            return true;
        }
        return false;
    }
}