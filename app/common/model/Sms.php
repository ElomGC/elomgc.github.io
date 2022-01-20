<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\Model;

class Sms extends Model
{

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
            if(!$this->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }

    public function getList($data = []){
        $map = $this;
        $map = !empty($data['to_uid']) ? $map->whereIn('to_uid',is_array($data['to_uid']) ? $data['to_uid'] : (string) $data['to_uid']) : $map;
        $map = !empty($data['fo_uid']) ? $map->whereIn('fo_uid',is_array($data['fo_uid']) ? $data['fo_uid'] : (string) $data['fo_uid']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('id desc')->paginate(empty($data['limit']) ? '24' : $data['limit']);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > 0){
            $getlist['data'] = getUserList($getlist['data'],'fo_uid');
            foreach ($getlist['data'] as $k => $v){
                $v['fo_uicon'] = '';
                $v['fo_uname'] = '系统消息';
                if($v['fo_uid'] > '0'){
                    if(empty($v['userdb'])){
                        $v['fo_uname'] = '用户已删除';
                    }else{
                        $v['fo_uicon'] = empty($v['userdb']['u_icon']) ? '' : $v['userdb']['u_icon'];
                        $v['fo_uname'] = empty($v['userdb']['u_niname']) ? $v['userdb']['u_name'] : $v['userdb']['u_niname'];
                    }
                }
                unset($v['userdb']);
                $v['addtime'] = date('Y-m-d H:i:s',(int) $v['addtime']);
                $getlist['data'][$k] = $v;
            }
        }
        $getlist['page'] = $page;
        return $getlist;
    }

    public function getOne($id){
        $getdata = $this->getList(['id' => (string) $id,'status' => 'a']);
        $getdata = $getdata['data']['0'];
        return $getdata;
    }
}