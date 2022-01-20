<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\facade\Db;
use worm\NodeFormat;

class ModelList extends R {
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'class'          => 'int',
        'title'        => 'string',
        'name'        => 'string',
        'keys'        => 'string',
        'status'        => 'tinyint',
        'addtime'      => 'int',
        'del_time'      => 'int',
    ];
    protected $type = [
        'addtime'  =>  'timestamp',
        'del_time'  =>  'timestamp',
    ];
    public function getList($data = [],$alllist = false){
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereIn('id',$data['id']);
        }
        if(!empty($data['keys'])){
            $map = $map->whereIn('keys',$data['keys']);
        }
        if(isset($data['class'])){
            $map = $map->whereClass($data['class']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if($alllist){
            $getlist = $map->order(empty($data['order']) ? 'id desc' : $data['order'])->select()->toArray();
        }else{
            $limit = [
                'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
            ];
            if(!empty($data['page'])){
                $limit['page'] = $data['page'];
            }
            $getlist = $map->order(empty($data['order']) ? 'id desc' : $data['order'])->paginate($limit);
            $page = $getlist->render();
            $getlist = Common::JobArray($getlist);
            $getlist['page'] = $page;
        }
        return $getlist;
    }
     //  清洗数据
    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = null;
        if(empty($_data['id'])){
            unset($_data['id']);
        }else{
            $old = $this->whereId($_data['id'])->find();
        }
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        $_data['keys'] = empty($old['keys']) ? $_data['name'] : $old['keys'];
        if(empty($old['del_time'])){
            unset($_data['del_time']);
        }
        return $_data;
    }
    public function setOne($data){
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  复制模块目录
    public function copydir($old,$data){
        $str = 'abstract,and,array,as,break,callable,case,catch,class,clone,php,const,continue,declare,default,die,do,echo,else,elseif,empty,enddeclare,endfor,endforeach,endif,endswitch,endwhile,eval,exit,extends,final,finally,for,foreach,function,global,goto,if,implements,include,instanceof,interface,isset,list,namespace,new,print,private,protected,public,require,return,static,switch,throw,trait,try,unset,use,var,while,xor,yield,insteadof';
        if(in_array($data['keys'],explode(',',$str))){
            return ['code' => '0','msg' => '非法目录名,请更换模组名称'];
        }
        if(is_dir(base_path("admin/controller/{$data['keys']}")) && base_path("common/model/{$data['keys']}") && base_path("api/controller/{$data['keys']}") && base_path("home/controller/{$data['keys']}") && base_path("member/controller/{$data['keys']}")){
            return ['code' => '0','msg' => '模块名已存在，请重新命名'];
        }
        $add = Common::copy_dir(base_path("admin/controller/{$old['keys']}"),base_path("admin/controller/{$data['keys']}"));
        if(!empty($add) ){
            return ['code' => '0','msg' => "创建【admin/controller/{$data['keys']}】目录失败,请检查权限"];
        }
        $add = Common::copy_dir(base_path("common/model/{$old['keys']}"),base_path("common/model/{$data['keys']}"));
        if(!empty($add) ){
            return ['code' => '0','msg' => "创建【common/model/{$data['keys']}】目录失败,请检查权限"];
        }
        $add = Common::copy_dir(base_path("api/controller/{$old['keys']}"),base_path("api/controller/{$data['keys']}"));
        if(!empty($add) ){
            return ['code' => '0','msg' => "创建【api/controller/{$data['keys']}】目录失败,请检查权限"];
        }
        $add = Common::copy_dir(base_path("{$old['keys']}"),base_path("{$data['keys']}"));
        if(!empty($add) ){
            return ['code' => '0','msg' => "创建【{$data['keys']}】目录失败,请检查权限"];
        }
        $add = Common::copy_dir(base_path("member/controller/{$old['keys']}"),base_path("member/controller/{$data['keys']}"));
        if(!empty($add) ){
            return ['code' => '0','msg' => "创建【member/controller/{$data['keys']}】目录失败,请检查权限"];
        }
        return ['code' => '1','msg' => "创建目录成功..."];
    }
    //  复制模块数据库
    public function copytable($old,$data){
        //  检测数据表
        $old_table = config('database.connections.mysql.prefix').$old['keys'].'_';
        $old_table_cont = $old_table.'content_';
        $new_table = config('database.connections.mysql.prefix').$data['keys'].'_';
        $table_list = Db::query("SHOW TABLE STATUS");
        foreach($table_list AS $rs){
            if(!preg_match("/^$old_table/i", $rs['Name']) || preg_match("/^$old_table_cont/i", $rs['Name'])){
                continue;
            }
            $array = Db::query("SHOW CREATE TABLE {$rs['Name']}")[0];
            $array['Create Table'] = str_replace($old_table,$new_table,$array['Create Table']);
            $auto = Common::data_trim(explode("\r\n",str_replace(array("\r\n", "\r", "\n"), "\r\n", $array['Create Table'])));
            $_auto = [];
            foreach ($auto as $k => $v){
                $v = explode(" ", $v);
                $_auto = array_merge($_auto,$v);
            }
            $auto = [];
            foreach ($_auto as $k => $v){
                $v = explode("=", $v);
                if(count($v) == 1){
                    continue;
                }
                $auto[$v['0']] = $v['1'];
            }
            $_auto = null;
            foreach ($auto as $k => $v){
                if($k == 'AUTO_INCREMENT'){
                    $_auto = "AUTO_INCREMENT={$v}";
                }
                continue;
            }
            $array['Create Table'] = str_replace($_auto,'AUTO_INCREMENT=0',$array['Create Table']);
            Db::execute($array['Create Table']);
        }
        return ['code' => '1','msg' => "创建数据表成功..."];
    }
    //  修改文件信息
    public function copyfile($old,$data){
        Common::edit_file_class(base_path("admin/controller/{$data['keys']}"),"\\".$old['keys'],"\\".$data['keys']);
        Common::edit_file_class(base_path("common/model/{$data['keys']}"),"\\".$old['keys'],"\\".$data['keys']);
        Common::edit_file_class(base_path("api/controller/{$data['keys']}"),"\\".$old['keys'],"\\".$data['keys']);
        Common::edit_file_class(base_path("{$data['keys']}/controller"),"\\".$old['keys'],"\\".$data['keys']);
        Common::edit_file_class(base_path("member/controller/{$data['keys']}"),"\\".$old['keys'],"\\".$data['keys']);
        return ['code' => '1','msg' => "创建文件成功..."];
    }
    //  复制权限
    public function copyauth($old,$data){
        $authmodel = new AuthRule();
        $authlist = $authmodel->whereLike('name',"{$old['keys']}.%")->select()->toArray();
        foreach ($authlist as $k => $v){
            $v['name'] = str_replace($old['keys'].'.',$data['keys'].'.',$v['name']);
            $authlist[$k] = $v;
        }
        $_authlist = NodeFormat::toLayer($authlist);
        foreach ($_authlist as $k => $v){
            $this->instauth($v);
        }
        $_authlist = NodeFormat::toLayer($authlist,'49');
        foreach ($_authlist as $k => $v){
            $this->instauth($v);
        }
        return ['code' => '1','msg' => "复制权限成功..."];
    }
    protected function instauth($data){
        $authmodel = new AuthRule();
        unset($data['id']);
        $add = $authmodel->getEditAdd($data);
        $_add = [];
        unset($add['node']);
        if($authmodel->where($add)->count() == 0){
            $_add = $authmodel->setOne($add);
        }
        if($_add !== false && !empty($data['node'])) {
            foreach ($data['node'] as $k => $v) {
                $v['pid'] = $_add['id'];
                $this->instauth($v);
            }
        }
        return true;
    }
    //  复制配置信息
    public function copyconfig($old,$data){
        $confModel = new Config();
        $file_list = $confModel->whereClass($old['keys'])->select()->toArray();
        $fileList = [];
        foreach ($file_list as $k => $v){
            unset($v['id']);
            unset($v['del_time']);
            $v['class'] = $data['keys'];
            $v['conf_value'] = null;
            $v['conf'] = str_replace($old['keys']."_",$data['keys']."_", $v['conf']);
            $fileList[] = $v;
        }
        if($confModel->saveAll($fileList)){
            return ['code' => '1','msg' => "复制配置信息成功..."];
        }
        return ['code' => '0','msg' => "复制配置信息失败..."];
    }
}