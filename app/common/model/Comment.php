<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use worm\NodeFormat;

class Comment extends R
{
    protected $schema = [
        'id' => 'int',
        'oid' => 'varchar',
        'pid' => 'int',
        'aid' => 'int',
        'mid' => 'int',
        'uid' => 'int',
        'jian' => 'int',
        'model' => 'varchar',
        'type' => 'varchar',
        'imgs' => 'text',
        'content' => 'text',
        'status' => 'int',
        'addtime' => 'int',
        'addip' => 'varchar',
    ];
    protected $type = [
        'imgs'  =>  'array',
    ];
    //  获取列表
    public function getList($data = [],$all = false,$undata = true){
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $map = $this;
        $map = !empty($data['oid']) ? $map->whereIn('oid',is_array($data['oid']) ? $data['oid'] : (string)$data['oid']) : $map;
        $map = !empty($data['aid']) ? $map->whereIn('aid',is_array($data['aid']) ? $data['aid'] : (string)$data['aid']) : $map;
        $map = !empty($data['type']) ? $map->whereType($data['type']) : $map;
        $map = !empty($data['model']) ? $map->whereModel($data['model']) : $map;
        $map = !empty($data['mid']) ? $map->whereIn('mid',is_array($data['mid']) ? $data['mid'] : (string)$data['mid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string)$data['uid']) : $map;
        $map = isset($data['pid']) ? $map->whereIn('pid',is_array($data['pid']) ? $data['pid'] : (string)$data['pid']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string)$data['id']) : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $map = $map->order('jian desc,id desc');
        if($all){
            $getlist = $map->select()->toArray();
            if($undata && count($getlist) > '0'){
                $_ids = array_column($getlist,'id');
                $_ids = $this->getDonID($_ids);
                $getlist = $this->Undata($_ids);
            }
        }else{
            $getlist = $map->paginate($limit);
            $getlist = Common::JobArray($getlist);
            if($undata && $getlist['total'] > '0'){
                $ids = array_column($getlist['data'],'id');
                $_ids = $this->getDonID($ids);
                $getlist['data'] = $this->Undata($getlist['data'],$_ids);
            }
        }
        $_getlist = $all ? $getlist : $getlist['data'];
        if(!empty($_getlist)){
            $_getlist = getUserList($_getlist);
            //  查询所有模块
            $_m = array_merge([],array_unique(array_column($_getlist,'model')));
            $_tlist = [];
            foreach ($_m as $k => $v){
                $_v = Common::del_file($_getlist,'model',$v);
                if($v == 'uid'){
                    continue;
                }else{
                    $_v = $this->getArticleTitle($_v,$v);
                }
                $_tlist[$v] = empty($_tlist[$v]) ? $_v : array_merge($_tlist[$v],$_v);
            }
            foreach ($_getlist as $k => $v){
                if($v != 'uid') {
                    $_v = $_tlist[$v['model']];
                    $_v = Common::del_file($_v, 'id', $v['aid']);
                    $v['art_title'] = $_v['0']['title'];
                }
                $v['addtime'] = date('Y-m-d H:i:s',(int) $v['addtime']);
                $v['u_name'] = empty($v['userdb']['u_name']) ? '' : $v['userdb']['u_name'];
                $v['u_uniname'] = empty($v['userdb']['u_uniname']) ? '' : $v['userdb']['u_uniname'];
                $v['u_uname'] = empty($v['userdb']['u_uname']) ? '' : $v['userdb']['u_uname'];
                $v['u_icon'] = empty($v['userdb']['u_icon']) ? '' : $v['userdb']['u_icon'];
                unset($v['userdb']);
                $_getlist[$k] = $v;
            }
        }
        if($all){
            $getlist = $_getlist;
        }else{
            $getlist['data'] = $_getlist;
        }
        return $getlist;
    }
    //  获取单条评论
    public function getOne($id){
        $getdata = $this->getList(['id' => $id,'status' => 'a']);
        return $getdata['data']['0'];
    }
    //  删除
    public function DeleteOne($data){
        $_oldlist = $this->getList(['id' => $data,'status' => 'a','limit' => '100']);
        $_oldlist = $_oldlist['total'] > '0' ? $_oldlist['data'] : [];
        if(empty($_oldlist)){
            return true;
        }
        $_imgs = array_column($_oldlist,'imgs');
        if(!empty($_imgs)){
            $_delimg = [];
            foreach ($_imgs as $k => $v){
                if(empty($v)){
                    continue;
                }
                $_v = array_column($v,'uri');
                $_delimg = array_merge($_delimg,$_v);
            }
            if(!empty($_delimg)){
                Upload::fileDel($_delimg);
            }
        }
        $_ids = array_column($_oldlist,'id');
        if($this->whereIn('id',$_ids)->delete()){
            foreach ($_oldlist as $k => $v){
                if($v['type'] == 'aid'){
                    Db::name("{$v['model']}_content_{$v['mid']}")->whereId($v['aid'])->dec('comment_num')->update();
                }
                continue;
            }
            return true;
        }
        return false;
    }
    //  获取所有回复ID
    protected function getDonID($data,$old = [])
    {
        if(!empty($data)){
            $_ids = array_unique($data);
            $Dids = $this->whereIn('pid',$_ids)->whereStatus('1')->column('id');
            if(!empty($Dids)){
                $_ids = array_merge($old,$Dids);
                return $this->getDonID($Dids,$_ids);
            }
        }
        return $old;
    }
    //  获取所有回复
    protected function Undata($data,$ids = []){
        if(empty($data)){
            return $data;
        }
        //  查询所有回复
        $_recont = empty($ids) ? [] : $this->getList(['id' => $ids],true,false);
        foreach ($data as $k => $v){
            $_v = NodeFormat::getChilds($_recont,$v['id']);
            if(!empty($_v)){
                foreach ($_v as $k1 => $v1){
                    if($v1['pid'] != $v['id']){
                        $_v1 = Common::del_file($_recont,'id',$v1['pid']);
                        if(!empty($_v1['0'])) {
                            $_name = !empty($_v1['0']['u_uniname']) ? $_v1['0']['u_uniname'] : $_v1['0']['u_name'];
                            $v1['title'] = "回复 {$_name}";
                        }
                    }
                    $_v[$k1] = $v1;
                }
            }
            $v['recont'] = empty($_v) ? [] : $_v;
            $data[$k] = $v;
        }
        return $data;
    }
    protected function getArticleTitle($data,$model){
        $_mid = array_unique(array_column($data,'mid'));
        $_data = [];
        foreach ($_mid as $k => $v){
            $_v = Common::del_file($data,'mid',$v);
            $_v = array_unique(array_column($_v,'aid'));
            $_v = Db::name("{$model}_content_{$v}")->whereIn('id',$_v)->field('title,id')->select()->toArray();
            $_data = array_merge($_data,$_v);
        }
        return $_data;
    }
}