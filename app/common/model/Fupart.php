<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use worm\NodeFormat;

class Fupart extends R
{
    protected $table;
    protected $name;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    protected $schema = [
        "id" => "int",
        "title" => "string",
        "pid" => "int",
        "limit" => "int",
        "title_num" => "int",
        "cont_num" => "int",
        "group_uid" => "string",
        "group_see" => "string",
        "group_edit" => "string",
        "pid_see" => "tinyint",
        "status" => "tinyint",
        "jumpurl" => "string",
        "keywords" => "string",
        "description" => "mediumtext",
        "password" => "string",
        "temp_late" => "string",
        "temp_head" => "string",
        "temp_list" => "string",
        "temp_cont" => "string",
        "temp_foot" => "string",
        'sort'      => 'int',
        'addtime'      => 'int',
        'del_time'      => 'int',
        "logo" => "string",
        "banber" => "string",
        "order" => "string",
    ];
    /**
     * 初始化模型信息
     */
    protected static $base_table;
    protected static $model_key;
    protected static $table_prefix;

    protected function initialize():void{
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$base_table = $array['0']['3']."_fupart_article";
        self::$model_key = $array['0']['3'];
        self::$table_prefix = config('database.connections.mysql.prefix');
        $this->table = self::$table_prefix.self::$model_key."_fupart";
        $this->name = self::$model_key."_fupart";
    }

    public function getList($data = [])
    {
        $map = $this;
        if(empty($data['getdeltime'])){
            $map = $map->withoutField('del_time,addtime');
            if(empty($data['del_time'])){
                $map = $map->whereDelTime('0');
            }else{
                $map = $map->where('del_time','>','0');
            }
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $map = !empty($data['group_edit']) ? $map->whereRaw('find_in_set(:groupid,group_edit) or `group_edit` is null or LENGTH(trim(group_edit)) = 0',['groupid' => $data['group_edit']]) : $map;
        $map = !empty($data['group_see']) ? $map->whereRaw('find_in_set(:groupid,group_see) or `group_see` is null or LENGTH(trim(group_see)) = 0',['groupid' => $data['group_see']]) : $map;
        $map = !empty($data['group_uid']) ? $map->whereRaw('find_in_set(:groupid,group_uid) or `group_uid` is null or LENGTH(trim(group_uid)) = 0',['groupid' => $data['group_uid']]) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;

        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        if(!empty($getlist)){
            $fids = array_column($getlist,'id');
            $fids = array_count_values(Db::name(self::$base_table)->whereDelTime('0')->whereIn('fuid',$fids)->column('fuid'));
            foreach ($getlist as $k => $v){
                $v['article_num'] = empty($fids[$v['id']]) ? '0' : $fids[$v['id']];
                $getlist[$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($id)
    {
        $getlist = $this->getList(['id' => $id,'status' => 'a']);
        return empty($getlist['0']) ? [] : $getlist['0'];
    }

    //  数据清洗
    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = empty($_data['id']) ? [] : $this->whereId($_data['id'])->find();
        if(empty($old)){
            unset($_data['id']);
        }
        $_data['logo'] = Common::setFile(empty($_data['logo']) ? null : $_data['logo'],empty($old['logo']) ? null : $old['logo']);
        $_data['banber'] = Common::setFile(empty($_data['banber']) ? null : $_data['banber'],empty($old['banber']) ? null : $old['banber']);
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        $_data['del_time'] = empty($old['del_time']) ? '0' : $old['del_time'];
        $_data['group_see'] = empty($_data['group_see']) ? null : implode(',',$_data['group_see']);
        $_data['group_edit'] = empty($_data['group_edit']) ? null : implode(',',$_data['group_edit']);
        $_data = Upload::editadd($_data);
        return $_data;
    }
    //  快速启用
    public function FastSwitch($data){
        if($data['_field'] == 'status' && $data['status'] == '0'){
            $getlist = $this->getList(['status' => 'a']);
            $getlist = NodeFormat::getChildsId($getlist,$data['id']);
            array_push($getlist,$data['id']);
            if($this->whereIn('id',$getlist)->update(['status' => '0'])){
                return true;
            }
            return false;
        }
        if($this->whereId($data['id'])->update([$data['_field'] => $data[$data['_field']]])){
            return true;
        }
        return false;
    }
    public function DeleteOne($id) {
        $id = is_array($id) ? $id : (int) $id;
        $_oldlist = $this->getList(['status' => 'a']);
        if(is_array($id)){
            $_oldid = [];
            foreach ($id as $k => $v){
                $_v = NodeFormat::getChildsId($_oldlist,$v);
                array_push($_v,(int) $v);
                $_oldid = array_unique(array_merge($_oldid,$_v));
            }
        }else{
            $_oldid = NodeFormat::getChildsId($_oldlist,$id);
            array_push($_oldid,$id);
        }
        $_oldlist = Common::del_file($_oldlist,'id',$_oldid);
        if(array_sum(array_column($_oldlist,'article_num')) > 0){
            return ['code' => '0','msg' => '栏目存在内容，禁止删除'];
        }
        $del_file = array_merge(array_column($_oldlist,'banber'), array_column($_oldlist,'logo'));
        Upload::fileDel(Upload::editadd(Common::del_null($del_file),false));
        if(!$this->whereIn('id',$_oldid)->delete()){
            return false;
        }
        return true;
    }
}