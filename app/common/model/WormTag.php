<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;
use app\facade\wormview\Upload;

class WormTag extends R
{
    public function getList($data = [])
    {
        $map = $this;
        $map = !empty($data['type']) ? $map->whereType($data['type']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $getlist = $map->paginate();
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $getlist['data'] = Upload::editadd($getlist['data'],false);
        }
        return $getlist;
    }

    public function getHomeList($data = [])
    {
        $map = $this;
        $map = !empty($data['type']) ? $map->whereType($data['type']) : $map;
        $map = !empty($data['TagName']) ? $map->whereIn('TagName',$data['TagName']) : $map;
        $map = !empty($data['TagModel']) ? $map->whereIn('TagModel',$data['TagModel']) : $map;
        $map = !empty($data['tempname']) ? $map->whereTempname($data['tempname']) : $map;
        $getlist = $map->select()->toArray();
        if(!empty($getlist)){
            $getlist = Upload::editadd($getlist,false);
        }
        return $getlist;
    }
}