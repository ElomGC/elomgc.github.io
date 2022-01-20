<?php
declare(strict_types = 1);

namespace app\common\model\form;

use app\common\model\ArticleBase;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use think\facade\Request;

class Form extends ArticleBase {

    public function getList($data = []){
        $map = Db::name($this->model_key.'_content_'.$data['mid']);
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('id desc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $getlist['data'] = getUserList($getlist['data']);
            foreach ($getlist['data'] as $k => $v){
                $v['addtime'] = date('Y-m-d H:i:s',(int) $v['addtime']);
                $v['u_name'] = empty($v['userdb']['u_name']) ? '' : $v['userdb']['u_name'];
                $v['u_uname'] = empty($v['userdb']['u_uname']) ? '' : $v['userdb']['u_uname'];
                $v['u_uiname'] = empty($v['userdb']['u_uiname']) ? '' : $v['userdb']['u_uiname'];
                $v['u_icon'] = empty($v['userdb']['u_icon']) ? '' : $v['userdb']['u_icon'];
                $v['u_phone'] = empty($v['userdb']['u_phone']) ? '' : $v['userdb']['u_phone'];
                unset($v['userdb']);
                $getlist['data'][$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($data = [],$ids = '',$uid = '')
    {
        $getlist = $this->getList($data);
        return $getlist['total'] > '0' ? $getlist['data']['0'] : [];
    }
    public function getEditAdd($data = [])
    {
        $fileList = Db::name('form_content_'.$data['mid'])->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = empty($_data['id']) ? [] : Db::name('form_content_'.$data['mid'])->whereId($_data['id'])->find();
        $old = empty($old) ? [] : $old;

        $img_data = [
            'oldimg' => empty($old['content']) ? null : Upload::editadd($old['content'],false),
            'newimg' => empty($_data['content']) ? null : $_data['content'],
        ];
        $_data['content'] = Upload::setEditOr($img_data, 'form/'.date('Y-m', time()));
        if(!empty($_data['res_content']) || !empty($old['res_content'])) {
            $img_data = [
                'oldimg' => empty($old['res_content']) ? null : Upload::editadd($old['res_content'], false),
                'newimg' => empty($_data['res_content']) ? null : $_data['res_content'],
            ];
            $_data['res_content'] = Upload::setEditOr($img_data, 'form/' . date('Y-m', time()));
        }
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        $_data['addip'] = empty($old['addip']) ? Request::ip() : $old['addip'];
        $_data['del_time'] = empty($old['del_time']) ? '0' : $old['del_time'];
        $_data['uid'] = empty($_data['uid']) &&  empty($old['uid']) ? '0' : $_data['uid'];
        return $_data;
    }
    public function setOne($data)
    {
        $map = Db::name('form_content_'.$data['mid']);
        if(empty($data['id'])){
            if(!$add = $map->insertGetId($data)){
                return false;
            }
        }else{
            if(!$map->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $map->whereId(empty($add) ? $data['id'] : $add)->find();
        return $res;
    }
    public function DeleteOne($data)
    {
        $data['status'] = 'a';
        $_oldlist = $this->getList($data);
        $_oldlist = $_oldlist['total'] > '0' ? $_oldlist['data'] : [];
        if(empty($_oldlist)){
            return true;
        }
        //  获取字段
        $filelist = Db::name($this->model_key."_filed")->whereIn('mid',$data['mid'])->select()->toArray();
        $filelist = Common::del_file($filelist,'form_type',['upload_img','editor','upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr']);
        if(!empty($filelist)){
            $editor = [];
            foreach ($filelist as $k => $v){
                $filelist[$k] = Common::ReadFile($v);
                if($filelist[$k]['type'] == 'editor'){
                    $editor[] = $filelist[$k]['file'];
                }
            }
            $_delfile = [];
            foreach ($_oldlist as $k => $v){
                $_v = Common::getReadFile($filelist,$v);
                if(!empty($editor)){
                    foreach ($editor as $k1 => $v1){
                        if(!empty($_v[$v1])){
                            $_delfile[] = Upload::img_list($_v[$v1]);
                            unset($_v[$v1]);
                        }
                        continue;
                    }
                }
                $_delfile[] = $_v;
            }
            $_delfile = Common::del_null($_delfile);
            Upload::fileDel($_delfile);
        }
        if(Db::name($this->model_key.'_content_'.$data['mid'])->whereIn('id',$data['id'])->delete()){
            return true;
        }
        return false;
    }
}