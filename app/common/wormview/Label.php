<?php
declare(strict_types = 1);
namespace app\common\wormview;

use app\common\model\ModelList;
use app\common\model\WormEdit;
use app\facade\hook\Common;
use think\facade\Cache;
use think\facade\View;

trait Label {
    protected function viewWeb($temps){
        $this->getLabel($temps);
        return view($temps);
    }
    //  提取页面标签
    protected function getLabel($file){
        $files = read_file($file);
        preg_match_all("/wormedit\.[0-9a-zA-z\-\_]*/", $files, $newfiles);
        if(empty($newfiles['0'])){
            return $file;
        }
        $labellist = [];
        foreach ($newfiles['0'] as $k => $v){
            $v = explode('.',$v);
            $labellist[] = $v['1'];
        }
        $WormModel = new WormEdit();
        $map = [
            'tempname' => config('view.view_open_path'),
            'model' => app('http')->getName(),
            'contr' => $this->request->controller(),
            'action' => $this->request->action(),
            'moid' => empty($this->getdata['moid']) ? 'a' : $this->getdata['moid'],
            'status' => '1',
            'name' => $labellist,
        ];
        $_labelname = "{$map['model']}_{$map['tempname']}_{$map['contr']}_{$map['action']}".implode('_',$labellist);
        if($this->LABEL){
            $map['status'] = 'a';
            Cache::delete($_labelname);
        }else if(Cache::has($_labelname)){
            View::assign(['wormedit' => Cache::get($_labelname)]);
            return $file;
        }
        $getlist = $WormModel->getList($map);
        $viewLabel = [];
        foreach ($labellist as $v){
            $viewLabel[$v] = $this->getLabelCont($getlist,$v,$map);
        }
        $viewLabel = \app\facade\wormview\Upload::editadd($viewLabel,false);
        if(!$this->LABEL){
            Cache::set($_labelname, $viewLabel);
        }
        View::assign(['wormedit' => $viewLabel]);
        return $file;
    }
    /**
     * @param $labellist 要读取的标签列表
     */
    protected function getLabelCont($viewlist,$labels,$base){
        $ids = Common::del_file($viewlist,'name',$labels);
        $temps = '';
        if(!empty($ids['0'])){
            $ids = $ids['0'];
            $wormEdit = new WormEdit();
            $temps = $wormEdit->getLabel($ids);
        }else {
            $base['moid'] = '0';
            $ids['name'] = $labels;
        }
        //  解析标签
        if($this->LABEL){
            $title = empty($ids['title']) ? '' : $ids['title'];
            $title = empty($title) ? $ids['name'] : $title;
            $type = empty($ids['type']) ? '' : $ids['type'];
            $tip = '编辑';
            $ids = empty($ids['id']) ? '0' : $ids['id'];
            $uri = $ids != '0' ? ['id' => $ids] : ['id'=>$ids,'name'=>$labels,'type'=>$type,'tempname'=>$base['tempname'],'model'=>$base['model'],'contr'=>$base['contr'],'action'=>$base['action'],'moid'=>$base['moid']];
            $uri = empty($type) ? url('admin/label/index',$uri)->build() : url("admin/label/{$type}",$uri)->build();
            $temps = "<div class='layout cx-pos-r'><div class='layout cx-pos-a cx-fex-c' style='top：0;z-index: 19841220'><div data-title='{$title}' data-uri='{$uri}' class='cx-button-s cx-bg-yellow cx-label' data-type='editlabel' >{$tip}{$title}</div></div></div>{$temps}";
        }
        return $temps;
    }


}