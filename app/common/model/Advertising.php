<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;
use app\facade\wormview\Upload;

class Advertising extends R
{
    public function getEditAdd($data = [])
    {
        $old = empty($data['id']) ? [] : $this->whereId($data['id'])->find();
        if(!empty($data['id'])){
            $old['cont'] = json_decode($old['cont'],true);
            $old['cont'] = Upload::editadd($old['cont'],false);
        }
        switch ($data['class']){
            case '1':
                $data['cont']['uri'] = Common::setFile(empty($data['uri']) ? '' : $data['uri'],empty($old['cont']['uri']) ? '' : $old['cont']['uri']);
                break;
            case '2':
                $_data = Common::compare_file(empty($data['cont']) ? [] : $data['cont'],empty($old['cont']) ? [] : $old['cont'],'uri');
                $_uridata = array_column($_data['new'],'uri');
                if(!empty($_uridata)){
                    foreach ($data['cont'] as $k => $v){
                        if(empty($v['uri'])){
                            unset($data['cont'][$k]);
                            continue;
                        }
                        if(!in_array($v['uri'],$_uridata)){
                            continue;
                        }
                        $v['uri'] = Upload::fileMove($v['uri']);
                        $data['cont'][$k] = $v;
                    }
                    $data['cont'] = Common::arraySort($data['cont'],'sort');
                    $data['cont'] = array_merge([],$data['cont']);
                    $_uridata = empty($_data['old']) ? [] : array_column($_data['old'],'uri');
                    if(!empty($_uridata)){
                        Upload::fileDel($_uridata);
                    }
                }
                break;
            case '3':
                $data['cont']['uri'] = Common::setFile(empty($data['uri']) ? '' : $data['uri'],empty($old['cont']['uri']) ? '' : $old['cont']['uri']);
                break;
        }
        $data = parent::getEditAdd($data);
        $data['cont'] = Upload::editadd($data['cont']);
        $data['cont'] = empty($data['cont']) ? '' : json_encode($data['cont'],JSON_UNESCAPED_UNICODE);
        return $data;
    }
    public function getList($data = [])
    {
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string)$data['id']) : $map;
        $map = !empty($data['class']) ? $map->whereClass($data['class']) : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
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
            foreach ($getlist['data'] as $k => $v){
                $v['cont'] = json_decode($v['cont'],true);
                $v['cont'] = Upload::editadd($v['cont'],false);
                $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $getlist['data'][$k] = $v;
            }
        }
        return $getlist;
    }
}