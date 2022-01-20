<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;

class UserAddress extends R {

    public function getList($data){
        $order = 'id desc';
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        if(!empty($data['uid'])){
            $map = $map->whereUid($data['uid']);
            $order = 'type desc';
        }
        if(!empty($data['key'])){
            $map = $map->whereLike('uname|uphone|uaddress',$data['key']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order($order)->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $getlist['data'] = getUserList($getlist['data']);
            foreach ($getlist['data'] as $k => $v){
                $v['u_name'] = $v['userdb']['u_name'];
                $v['u_uniname'] = $v['userdb']['u_uniname'];
                $v['u_uname'] = $v['userdb']['u_uname'];
                $v['u_icon'] = $v['userdb']['u_icon'];
                unset($v['userdb']);
                $getlist['data'][$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($id){
       $getlist = $this->getList(['id' => $id]);
       return $getlist['total'] > '0' ? $getlist['data']['0'] : false;
    }
    //  添加地址
    public function setOne($data){
        $cModel = new Chinacode();
        $chinacode = $cModel->getOne($data['zoneid']);
        $data['cartid'] = $chinacode['cartid'];
        $data['uaddress'] = $chinacode['cartname'].",".$data['uaddress'];
        $_old = $this->whereUid($data['uid'])->count();
        $data['type'] = empty($data['type']) ? '0' : $data['type'];
        if($_old == 0){
            $data['type'] = '1';
        } else if($data['type'] = '1'){
            $this->whereUid($data['uid'])->whereType('1')->update(['type'=>'0']);
        }
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



}