<?php
declare(strict_types = 1);
namespace app\common\model;

use app\facade\hook\Common;
use think\facade\Db;
use think\Model;

class Order extends Model {

    public function setOne($data,$name,$file = 'groupid'){
        $mid = array_merge([],array_unique(array_column($data,'mid')));
        $aid = array_merge([],array_unique(array_column($data,'aid')));
        $mid = $mid['0'];
        $aid = $aid['0'];
        $res = Db::name("order_{$name}")->whereMid($mid)->whereAid($aid)->select()->toArray();
        if(empty($res)){
            Db::name("order_{$name}")->insertAll($data);
        }else{
            $_data = $data;
            foreach ($res as $k => $v){
                foreach ($data as $k1 => $v1){
                    if($v[$file] == $v1[$file] && $v['parameter'] == $v1['parameter'] && $v['money_type'] == $v1['money_type']){
                        unset($data[$k1]);
                        $v = array_merge($v,$v1);
                        Db::name("order_{$name}")->whereId($v['id'])->update($v);
                    }
                }
            }
            if(!empty($data)){
                Db::name("order_{$name}")->insertAll($data);
            }
            $res = Db::name("order_{$name}")->whereMid($mid)->whereAid($aid)->select()->toArray();
            //  检测不需要的数据
            $_file = $file == 'groupid' ? 'chinacode' : 'groupid';
            foreach ($res as $k => $v){
                $_v = Common::del_file($_data,$file,$v[$file]);
                $_v = !empty($_v) ? Common::del_file($_data,$_file,$v[$_file]) : [];
                $_v = !empty($_v) ? Common::del_file($_v,'parameter',$v['parameter']) : [];
                $_v = !empty($_v) ?Common::del_file($_v,'money_type',$v['money_type']) : [];
                if(!empty($_v)){
                    unset($res[$k]);
                }
                continue;
            }
            if(!empty($res)){
                $_id = array_column($res,'id');
                Db::name("order_{$name}")->whereIn('id',$_id)->delete();
            }
        }
        return true;
    }
    public function getOne($data,$name){
        $res = Db::name("order_{$name}")->whereMid($data['mid'])->whereAid($data['id'])->select()->toArray();
        return $res;
    }
    public function getList($data,$name){
        $res = Db::name("order_{$name}")->whereIn('mid',$data['mid'])->whereIn('aid',$data['aid'])->select()->toArray();
        return $res;
    }
}