<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;

class UserApi extends R {

    //  查询openid
    public function getList($data = []){
        $map = $this;
        if(!empty($data['unionid'])){
            $map = $map->whereIn('unionid',$data['unionid']);
        }else if(!empty($data['openid'])){
            $map = $map->whereOpenid($data['openid']);
        }
        if(!empty($data['uid'])){
            $map = $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']);
        }
        if(!empty($data['id'])){
            $map = $map->whereId($data['id']);
        }
        if(!empty($data['type'])){
            $map = $map->whereIn('type',$data['type']);
        }
        $getlist = $map->select()->toArray();
        return $getlist;
    }
    public function getNewList($data = []){
        $map = $this;
        if(!empty($data['unionid'])){
            $map = $map->whereIn('unionid',$data['unionid']);
        }else if(!empty($data['openid'])){
            $map = $map->whereOpenid($data['openid']);
        }
        if(!empty($data['uid'])){
            $map = $map->whereIn('uid',$data['uid']);
        }
        if(!empty($data['id'])){
            $map = $map->whereId($data['id']);
        }
        if(!empty($data['type'])){
            $map = $map->whereIn('type',$data['type']);
        }
        $getlist = $map->order('id desc')->paginate(empty($data['limit']) ? '24' : $data['limit']);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        $getlist['page'] = $page;
        return $getlist;
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
        $old = empty($_data['id']) ? [] : $this->whereId($_data['id'])->find();
        if(empty($old)){
            unset($_data['id']);
        }
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['time'];
        $_data['edittime'] = time();
        return $_data;
    }
    //  保存OpenID
    public function setOpenidList($data){
        $oldlist = $this->whereIn('openid',$data)->column('openid');
        $add = empty($oldlist) ? $data : array_diff($data,$oldlist);
        if(!empty($add)){
            $_add = [];
            foreach ($add as $k => $v){
                $_add[] = ['openid' => $v,'type' => 'wx'];
            }
            $this->saveAll($_add);
        }
        return true;
    }
}