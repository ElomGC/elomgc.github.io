<?php
declare(strict_types = 1);

namespace app\common\wormview;

use app\facade\hook\Common;
use app\facade\model\WormTag;
use think\facade\Cache;
use think\facade\Request;
use worm\NodeFormat;

trait Tag
{
    /**
     * @param $data 调用内容列表
     * @return mixed
     */
    protected function TagAritclelist($data){
        if(Cache::has($data['setname'])){
            return Cache::get($data['setname']);
        }
        $model = "app\\common\\model\\{$data['model']}\\Article";
        $model = new $model;
        if(!empty($data['fid'])){
            $_part = $this->TagPartlist(['model' => $data['model'],'mid' => $data['mid'],'id' => $data['fid']]);
            $_part = Common::del_file($_part,'pid_see','1');
            $data['fid'] = array_column($_part,'id');
        }
        $res = $model->getList(['mid' => $data['mid'],'fid' => $data['fid'],'jian' => $data['jian'],'picurl' => $data['picurl'],'getFile' => $data['getFile'],'limit' => $data['limit'],'order' => empty($data['order']) ? '' : $data['order']]);
        if($res['total'] > '0'){
            foreach ($res['data'] as $k => $v){
                if(!empty($v['title']) && !empty($data['title_num'])) {
                    $v['title'] = get_word($v['title'], (int) $data['title_num'],1,'');
                }
                if(!empty($v['description']) && !empty($data['description_num'])) {
                    $v['description'] = get_word($v['description'], (int) $data['description_num']);
                }
                $res['data'][$k] = $v;
            }
        }
        if(!empty($data['setname'])){
            Cache::set($data['setname'],$res);
        }
        return $res;
    }
    /**
     * @param $data 调用栏目列表
     * @return array|mixed
     */
    protected function TagPartlist($data){
        if(!empty($data['setname']) && Cache::has($data['setname'])){
            return Cache::get($data['setname']);
        }
        $model = "app\\common\\model\\{$data['model']}\\Part";
        $model = new $model;
        $res = $model->getPartList(['mid' => $data['mid']]);
        if(!empty($data['id'])){
            $data['id'] = explode(',',$data['id']);
            $_read = Common::del_file($res,'id',$data['id']);
            $_res = [];
            foreach ($data['id'] as $k => $v){
                $_res = array_merge($_res,NodeFormat::getChilds($res,$v));
            }
            $res = array_merge($_read,$_res);
        }
        foreach ($res as $k => $v){
            $v['uri'] = getUrl('part-'.$v['id'],app('http')->getName(),[],true);
            $res[$k] = $v;
        }
        if(!empty($data['setname'])){
            Cache::set($data['setname'],$res);
        }
        return $res;
    }
    protected function TagGetNavList($data){
        if(!empty($data['setname']) && Cache::has($data['setname'])){
            return Cache::get($data['setname']);
        }
        $model = "app\\common\\model\\Nav";
        $model = new $model;
        $res = $model->getHomeList(['class' => (int) $data['cid'],'id' => empty($data['id']) ? '' : $data['id']]);
        if(!empty($data['setname'])){
            Cache::set($data['setname'],$res);
        }
        return $res;
    }
    protected function TagAdvertising($data){
        $model = "app\\common\\model\\Advertising";
        $model = new $model;
        $res = $model->getOne($data['id']);
        return $res['cont'];
    }

    /**
     * @param $tag 标签信息
     * @param $name 标签类型
     * @return mixed
     */
    protected function GetTagRead($tagdata,$type,$_label = false){
        $map = [
            'TagName' => $tagdata['TagName'],
            'TagModel' => $tagdata['TagModel'],
            'TagMid' => empty($tagdata['TagMid']) ? '0' : $tagdata['TagMid'],
            'TagFid' => empty($tagdata['TagFid']) ? '0' : $tagdata['TagFid'],
            'TagFiled' => empty($tagdata['TagFiled']) ? '0' : $tagdata['TagFiled'],
            'TagLimit' => empty($tagdata['TagLimit']) ? '0' : $tagdata['TagLimit'],
            'TagOrder' => empty($tagdata['TagOrder']) ? '0' : $tagdata['TagOrder'],
            'type' => $type,
            'tempname' => config('view.view_open_path'),
            'model' => app('http')->getName(),
            'contr' => Request::controller(),
            'action' => Request::action(),
        ];
        //  获取标签缓存
        $getdata = Request::param();
        $getdata = array_flip($getdata);
        $getdata = array_shift($getdata);
        if(!empty($getdata)) {
            $getdata = explode('/', $getdata);
            $getdata = implode('_', $getdata);
        }
        $_tpname = Cache::has($map['tempname']) ? Cache::get($map['tempname']) : [];
        $res['name'] = $map['model'].'_'.$map['contr'].'_'.$map['action'].'_'.$map['type'].'_'.$map['TagModel'].'_'.$map['TagName'].$getdata;
        if($_label){
            Cache::delete($res['name']);
            $_tpname[$res['name']] = [];
        }
        if(empty($_tpname[$res['name']])){
            $_old = WormTag::getHomeList($map);
            //  根据当前位置获取当前标签
            $_old = Common::del_file($_old,'model',$map['model']);
            $_old = Common::del_file($_old,'contr',$map['contr']);
            $_old = Common::del_file($_old,'action',$map['action']);
            $_old = count($_old) < '1' ? [] : $_old['0'];
            if(empty($_old)) {
                $_old = [
                    'status' => '1'
                ];
            }else{
                $_old['conf'] = empty($_old['conf']) ? [] : json_decode($_old['conf'],true);
            }
            $_tpname[$res['name']] = $_old;
            Cache::set($map['tempname'],$_tpname);
        }
        $res['tempname'] = $map['tempname'];
        $res['data'] = $_tpname[$res['name']];
        return $res;
    }

}