<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;

class SpecialArticle extends R
{
    public function setOne($data)
    {
        $mid = array_merge([],array_unique(array_column($data,'sid')));
        $model = array_merge([],array_unique(array_column($data,'model')));
        $aid = array_merge([],array_unique(array_column($data,'aid')));
        $model = $model['0'];
        $sid = $mid['0'];
        $aid = $aid['0'];
        $res = $this->whereModel($model)->whereSid($sid)->whereAid($aid)->select()->toArray();
        if(empty($res)){
            $this->saveAll($data);
        }else{
            $_data = $data;
            foreach ($res as $k => $v){
                foreach ($data as $k1 => $v1){
                    if($v['model'] == $v1['model'] && $v['sid'] == $v1['sid'] && $v['aid'] == $v1['aid']){
                        unset($data[$k1]);
                    }
                }
            }
            if(!empty($data)){
                $this->saveAll($data);
            }
            $res = $this->whereModel($model)->whereSid($sid)->whereAid($aid)->select()->toArray();
            foreach ($res as $k => $v){
                $_v = Common::del_file($_data,'model',$v['model']);
                $_v = !empty($_v) ? Common::del_file($_v,'sid',$v['sid']) : [];
                $_v = !empty($_v) ?Common::del_file($_v,'aid',$v['aid']) : [];
                if(!empty($_v)){
                    unset($res[$k]);
                }
                continue;
            }
            if(!empty($res)){
                $_id = array_column($res,'id');
                $this->whereIn('id',$_id)->delete();
            }
        }
        return true;
    }

    public function getList($data = []){
        $map = $this;
        $map = !empty($data['sid']) ? $map->whereIn('sid',is_array($data['sid']) ? $data['sid'] : (string) $data['sid']) : $map;
        $map = !empty($data['aid']) ? $map->whereIn('aid',is_array($data['aid']) ? $data['aid'] : (string) $data['aid']) : $map;
        $map = !empty($data['model']) ? $map->whereIn('model',is_array($data['model']) ? $data['model'] : (string) $data['model']) : $map;
        if(empty($data['status'])){
            $map = $map->whereStatus('1');
        }else if(!isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('id desc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $_model = array_unique(array_column($getlist['data'],'model'));
            $_getlist = [];
            foreach ($_model as $k => $v){
                $aModel = "app\\common\\model\\{$v}\\Article";
                $aModel = new $aModel;
                $_vid = array_unique(array_column($getlist['data'],'aid'));
                $_vid = $aModel->getList(['id' => $_vid,'limit' => count($_vid)]);
                $_getlist[$v] = $_vid['data'];
            }
            foreach ($getlist['data'] as $k => $v){
                $_v = Common::del_file($_getlist[$v['model']],'id',$v['aid']);
                $_v = $_v['0'];
                unset($_v['id']);
                $_special_name = Common::del_file($_v['special'],'sid',$v['sid']);
                $_v['special_name'] = $_special_name['0']['special_name'];
                $getlist['data'][$k] = array_merge($_v,$v);
            }
        }
        return $getlist;
    }

    public function DeleteOne($ids){
        if($this->whereIn('id',is_array($ids) ? $ids : (string) $ids)->delete()){
            return true;
        }
        return false;
    }
}