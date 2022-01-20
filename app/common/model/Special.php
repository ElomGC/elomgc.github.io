<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;

class Special extends R
{

    public function getEditAdd($data = [])
    {
        $_data = parent::getEditAdd($data);
        $old = empty($_data['id']) ? [] : $this->whereId($_data['id'])->find();
        $_data['logo'] = Common::setFile(empty($_data['logo']) ? null : $_data['logo'],empty($old['logo']) ? null : $old['logo']);
        $_data['banber'] = Common::setFile(empty($_data['banber']) ? null : $_data['banber'],empty($old['banber']) ? null : $old['banber']);
        $_data = Upload::editadd($_data);
        return $_data;
    }

    public function getList($data = []){
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['cid']) ? $map->whereIn('cid',is_array($data['cid']) ? $data['cid'] : (string) $data['cid']) : $map;
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
        $getlist = $map->order('sort desc,id desc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $_cid = array_unique(array_column($getlist['data'],'cid'));
            $_classlist = Db::name('special_class')->whereIn('id',$_cid)->field('title,id')->select()->toArray();
            $_commentlist = Db::name('comment')->whereType('mid')->whereIn('aid',$_cid)->field('aid,id')->select()->toArray();
            $_sid =  array_unique(array_column($getlist['data'],'id'));
            $_articlelist = Db::name('special_article')->whereIn('sid',$_sid)->whereStatus('1')->whereDelTime('0')->field('aid,sid,id')->select()->toArray();
            foreach ($getlist['data'] as $k => $v){
                $_v = Common::del_file($_classlist,'id',$v['cid']);
                $v['ctitle'] = empty($_v['0']['title']) ? '分类已删除' : $_v['0']['title'];
                $_v = Common::del_file($_commentlist,'aid',$v['id']);
                $v['comment_num'] = count($_v);
                $_v = Common::del_file($_articlelist,'sid',$v['id']);
                $v['article_num'] =  count($_v);
                $getlist['data'][$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($id){
        $getdata = $this->getList(['id' => $id]);
        return empty($getdata['data']['0']) ? [] : $getdata['data']['0'];
    }
    public function DeleteOne($ids){
        $ids = is_array($ids) ? $ids : explode(',',$ids);
        $dellist = $this->getList(['id' => $ids,'status' => 'a','limit' => count($ids)]);
        if($dellist['total'] < '1'){
            return true;
        }
        $dellist = Upload::editadd($dellist['data'],false);
        $_img = array_column($dellist,'banber');
        $_img = Common::del_null(array_merge(array_column($dellist,'logo'),$_img));
        Upload::fileDel($_img);
        $ids = array_column($dellist,'id');
        if($this->whereIn('id',$ids)->delete()){
            $aModel = new SpecialArticle();
            $aModel->whereIn('sid',$ids)->delete();
           return true;
        }
        return false;
    }
}