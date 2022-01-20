<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\Model;
use worm\NodeFormat;

class WormEdit extends Model {

    public function getList($data = []){
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereIn('id',$data['id']);
        }
        if(!empty($data['tempname'])){
            $map = $map->whereTempname($data['tempname']);
        }
        if(!empty($data['model'])){
            $map = $map->whereModel($data['model']);
        }
        if(!empty($data['contr'])){
            $map = $map->whereContr($data['contr']);
        }
        if(!empty($data['action'])){
            $map = $map->whereAction($data['action']);
        }
        if(isset($data['moid']) && $data['moid'] != 'a'){
            $map = $map->whereMoid($data['moid']);
        }
        if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if(!empty($data['name'])){
            $map = $map->whereIn('name',$data['name']);
        }
        $getlist = $map->select()->toArray();
        return $getlist;
    }
    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v) {
            if (isset($data[$v])) {
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = empty($data['id']) ? [] : $this->whereId($_data['id'])->find()->toArray();
        if(empty($old)){
            unset($_data['id']);
        }
        return $_data;
    }
    //  保存数据
    public function setOne($data){
        $data = $this->getEditAdd($data);
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            $old = $this->whereId($data['id'])->find();
            if(!$old->save($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  标签解析
    public function getLabel($data):string
    {
        $data['conf'] = empty($data['conf']) ? [] : json_decode($data['conf'],true);
        $_conf = $data['conf'];
        $conttemp = '';
        $datalist = $labellist = [];
        switch ($data['type']){
            case 'labelvideo':
                $conttemp = !empty($_conf['conttemp']) ? $_conf['conttemp'] : "<video class='layout' src=\"{\$rs['uri']}\" controls='controls'></video>";
                $labellist = $this->GetTempLabel($conttemp);
                $datalist = json_decode($_conf['imglist'],true);
                break;
            case 'labelimgs':
                $conttemp = !empty($_conf['conttemp']) ? $_conf['conttemp'] : "<img class='cx-img-responsive' src=\"{\$rs['uri']}\" alt=\"{\$rs['title']}\">";
                $labellist = $this->GetTempLabel($conttemp);
                $datalist = json_decode($_conf['imglist'],true);
                break;
            case 'labeltext':
                return $_conf;
                break;
            case 'labelcktext':
                return $_conf;
                break;
            case 'partedit':
                $conttemp = !empty($_conf['conttemp']) ? $_conf['conttemp'] : "<h3>{\$rs['title']}</h3>";
                $labellist = $this->GetTempLabel($conttemp);
                $modellist = new ModelList();
                $_modellist = $modellist->getList(['id' => (string)$data['moid']],true);
                $_modellist = $_modellist['0'];
                $map = [
                    'fid' => empty($_conf['fid']) ? null : $_conf['fid'],
                    'mid' => empty($_conf['mid']) || $_conf['mid'] == 'a' ? null : $_conf['mid'],
                    'jian' => $_conf['jian'] == '1' && !empty($_conf['jian_lavel']) ? $_conf['jian_lavel'] : '',
                    'order' => $_conf['order'],
                    'limit_num' => empty($_conf['limit_num']) ? '1' : $_conf['limit_num'],
                    'limit' => empty($_conf['limit']) ? '20' : $_conf['limit'],
                ];
                $datalist = $this->getArticlelist($map,$_modellist);
                foreach ($datalist as $k => $v) {
                    $v['title'] = empty($_conf['title_num']) ? $v['title'] : get_word($v['title'], (int) $_conf['title_num']);
                    $v['description'] = empty($_conf['description_num']) ? $v['description'] : get_word($v['description'], (int) $_conf['description_num'], false);
                    $datalist[$k] = $v;
                }
                break;
            case 'partlist':
                $conttemp = !empty($_conf['conttemp']) ? $_conf['conttemp'] : "<a href='/part-{\$rs['id']}.html'>{\$rs['title']}</a>";
                $labellist = $this->GetTempLabel($conttemp);
                $modellist = new ModelList();
                $_modellist = $modellist->getList(['id' => (string)$data['moid']],true);
                $_modellist = $_modellist['0'];
                $map = [
                    'id' => empty($_conf['id']) ? null : $_conf['id'],
                    'mid' => $_conf['mid'] == 'a' ? null : $_conf['mid'],
                    'class' => !isset($_conf['class']) ? 'n' : $_conf['class'],
                    'keys' => $_modellist['keys'],
                ];
                $datalist = $this->getPartlist($map);
                break;
            case 'partone':
                $conttemp = !empty($_conf['conttemp']) ? $_conf['conttemp'] : "<a href='/part-{\$rs['id']}.html'>{\$rs['title']}</a>";
                $labellist = $this->GetTempLabel($conttemp);
                $modellist = new ModelList();
                $_modellist = $modellist->getList(['id' => (string)$data['moid']],true);
                $_modellist = $_modellist['0'];
                $map = [
                    'id' => empty($_conf['id']) ? null : $_conf['id'],
                    'mid' => $_conf['mid'] == 'a' ? null : $_conf['mid'],
                    'class' => 'n',
                    'keys' => $_modellist['keys'],
                ];
                $datalist = $this->getPartlist($map);
                if(!empty($_conf['description_num'])) {
                    foreach ($datalist as $k => $v) {
                        $v['description'] = get_word($v['description'], (int) $_conf['description_num'], false);
                        $datalist[$k] = $v;
                    }
                }
                break;
        }
        $_data = $this->SetTempData($datalist,$conttemp,$labellist);
        return $_data;
    }

    /**
     * @param $temps 模板代码
     * @return array    返回标签列表，如果一维数组，则返回为字符串，多维数组为数组格式
     */
    protected function GetTempLabel($temps):array
    {
        preg_match_all("/(?<={\\\$rs)[^}]+/",$temps,$newlabel);
        $newlabel  = $newlabel[0];
        $_newlabel = [];
        foreach ($newlabel as $k => $v){
            preg_match_all("#\['(.*?)'\]#us",$v,$_v);
            if(empty($_v['1'])){
                preg_match_all("#\[(.*?)\]#us",$v,$_v);
            }
            $v = $_v['1'];
            $_newlabel[] = count($v) > '1' ? $v : $v['0'];
        }
        return $_newlabel;
    }
    //  处理数组
    protected function getArrayList($data,$file,$num = '0'):array
    {
        $_data = [];
        foreach ($file as $key => $val) {
            if($key == $num && isset($data[$val])){
                if($key < count($file) && is_array($data[$val])){
                    $_data[$val] = $this->getArrayList($data[$val],$file,$num + 1);
                }else{
                    $_data[$val] = $data[$val];
                }
            }
        }
        return  $_data;
    }
    protected function SetTempData($data,$temps,$labellist):string
    {
       if(empty($temps) && empty($labellist)){
           return '';
       }
        $_data = [];
        foreach ($data as $k => $v){
            foreach ($labellist as $k1 => $v1){
                if(!is_array($v1)){
                    $_data[$k][$v1] = empty($v[$v1]) ? '' : $v[$v1];
                }else{
                    $_v = $this->getArrayList($v,$v1);
                    $_data[$k] = array_merge($_data[$k],$_v);
                }
            }
        }
        $_data = empty($_data) ? $data : $_data;
        //  写入模板
        $fhvalus = '';
        $_temps = str_replace(array("'][", "]['", "']['"),"][", $temps);//获得所有标签;
        $_temps = str_replace(array("{\$rs['","{\$rs[",),"\$rs[", $_temps);//获得所有标签;
        $_temps = str_replace(array("']}","]}",),"]", $_temps);//获得所有标签;
        $_temps = $this->sizedata($_temps);//获得所有标签;
        //  重新赋值
        foreach ($_data as $k => $rs){
            $htmlcode = addslashes($_temps);
            eval("\$htmlcode=\"{$htmlcode}\";");
            $fhvalus .= StripSlashes($htmlcode);
        }
        return $fhvalus;
    }
    protected function sizedata($data):string {
        $data = trim($data); //清除字符串两边的空格
        $data = htmlentities($data,ENT_SUBSTITUTE); //清除字符串两边的空格
        $data = preg_replace("/\n/","",$data);
        $data = preg_replace("/\r/","",$data);
        $data = str_replace(" &amp;nbsp; ","&nbsp;",$data);
        $data = str_replace("&nbsp;","",$data);
        $data = html_entity_decode($data, ENT_HTML5, "utf-8");
        return $data;
    }
    //  查询模型
    public function getModellist($data){
        $mdodel = "app\\common\\model\\".$data['keys']."\\Artmodel";
        $mdodel = new $mdodel;
        return $mdodel->getList();
    }
    //  查询模型
    public function getPartlist($data){
        $mdodel = "app\\common\\model\\".$data['keys']."\\Part";
        $mdodel = new $mdodel;
        $_class = !isset($data['class']) ? 'a' : $data['class'];
        $data['class'] = isset($data['class']) && $data['class'] == 'n' ? 'a' : $data['class'];
        $partlist = $mdodel->getList($data);
        if(!in_array($_class,['n','1'])){
            $partlist = NodeFormat::toList($partlist);
        }
        return $partlist;
    }
    //  查询内容
    public function getArticlelist($map,$models):array
    {
        $mdodel = "app\\common\\model\\".$models['keys']."\\Article";
        $mdodel = new $mdodel;
        $articellist = $mdodel->getList($map);
        return $articellist['data'];
    }
}