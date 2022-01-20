<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\facade\Db;

class SpecialClassBase extends R
{
    protected $name = 'special_class';
    public function getList($data = []){
        $map = $this;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a') {
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        if(!empty($getlist)){
            $_id = array_column($getlist,'id');
            $_spelist = Db::name('special')->whereIn('cid',$_id)->field('id,cid')->select()->toArray();
            foreach ($getlist as $k => $v){
                $v['special_num'] = count(Common::del_file($_spelist,'cid',$v['id']));
                $getlist[$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($id)
    {
        $getlist = $this->getList(['id' => $id]);
        return empty($getlist['0']) ? [] : $getlist['0'];
    }
}