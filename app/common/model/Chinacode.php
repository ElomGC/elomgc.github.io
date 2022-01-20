<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\Model;
use worm\NodeFormat;

class Chinacode extends R {
    public function getList($data = []){
        $map = $this;
        if(isset($data['parzoneid'])){
            if(is_array($data['parzoneid'])){
                $map = $map->whereIn('parzoneid', $data['parzoneid']);
            }else{
                $map = $map->whereParzoneid($data['parzoneid']);
            }
        }
        $map = !empty($data['zoneid']) ? $map->whereIn('zoneid',is_array($data['zoneid']) ? $data['zoneid'] : (string) $data['zoneid']) : $map;
        $map = !empty($data['zonelevel']) ? $map->where('zonelevel','<=',$data['zonelevel']) : $map;

        $getlist = $map->withoutField('id,sort')->select()->toArray();
        return $getlist;
    }
    public function getPiplist($zoneid){
        $old = $this->whereZoneid($zoneid)->find()->toArray();
        $_cartid = $cartid['zoneid'] = explode(',',$old['cartid']);
        $cartid = $this->getList($cartid);
        $parzoneid = ['parzoneid' => array_unique(array_column($cartid,'parzoneid'))];
        $parzoneid = $this->getList($parzoneid);

        $resList = [];
        foreach ($_cartid as $k => $v){
            $new = Common::del_file($cartid,'zoneid',$v);
            $new = Common::del_file($parzoneid,'parzoneid',$new['0']['parzoneid']);
            $resList[] = [
                'value' => $v,
                'list' => $new,
            ];
        }
        return $resList;
    }
    //  查询所有下级地区
    public function getLowerList($data){
        $_data = empty($data['getlist']) ? [] : $data['getlist'];
        unset($data['getlist']);
        $getlist = $this->getList($data);
        $data['getlist'] = empty($getlist) ? $_data : array_merge($_data,$getlist);
        if(!empty($getlist)){
            $data['parzoneid'] = array_column($getlist,'zoneid');
            return $this->getLowerList($data);
        }
        return $data['getlist'];
    }
    //  查询当前地址信息
    public function getOne($zoneid){
        $getlist = $this->whereZoneid($zoneid)->find();
        if(!empty($getlist)){
            $getlist = $getlist->toArray();
            unset($getlist['id']);
            unset($getlist['sort']);
        }
        return $getlist;
    }
}