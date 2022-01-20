<?php
declare(strict_types = 1);

namespace app\common\model;

use think\facade\Db;
use think\Model;

class UserFile extends Model {
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $schema = [
        "id" => 'int',
        "mid" => 'int',
        "sql_file" => "string",
        "sql_type" => "string",
        "form_title" => "string",
        "form_text" => "string",
        "form_required" => "tinyint",
        "form_required_list" => "string",
        "form_unit" => "string",
        "from_default" => "string",
        "form_type" => "string",
        "form_class" => "string",
        "form_data" => "text",
        "form_tip" => "string",
        "form_group" => "string",
        "form_js" => "text",
        "group_see" => 'string',
        "group_edit" => "string",
        "status" => "tinyint",
        "sort" => "int",
        "list_show" => "tinyint",
        "cont_show" => "tinyint",
        "addtime" => "int",
        "del_time" => "int",
    ];
    protected $type = [
        'addtime'  =>  'timestamp',
        'del_time'  =>  'timestamp',
    ];
    protected $readonly = ['sql_file', 'sql_type'];
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
        $_data['addtime'] = empty($old['addtime']) ? time() : strtotime($old['addtime']);
        $_data['del_time'] = empty($old['del_time']) ? '0' : strtotime($old['del_time']);
        $_data['group_see'] = empty($_data['group_see']) ? null : implode(',',$_data['group_see']);
        $_data['group_edit'] = empty($_data['group_edit']) ? null : implode(',',$_data['group_edit']);
        return $_data;
    }
    //  检测字段是否存在
    public function checkFiled($data){
        $userModel = new User();
        $file_list = $userModel->getTableFields();
        $_file_list = Db::name('user_data')->getTableFields();
        if(empty($data['sql_file']) || in_array($data['sql_file'],$file_list) || in_array($data['sql_file'],$_file_list)){
            return false;
        }
        return true;
    }
    //  保存数据
    public function setOne($data){
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
            if(!$this->addTabelFiled($data)){
                $add->delete();
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
    //  添加数据表字段
    public function addTabelFiled($data){
        $table_prefix = config('database.connections.mysql.prefix');
        $tabelname = $table_prefix.'user_data';
        $result = Db::query("SHOW TABLES LIKE '{$tabelname}'");
        if(empty($result)){
            return false;
        }
        $sql = "ALTER TABLE `{$tabelname}` ADD `{$data['sql_file']}` {$data['sql_type']} COMMENT '{$data['form_tip']}';";
        try {
            Db::execute($sql);
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }
    //  获取所有字段
    public function getList($data = []){
        $map = $this;
        $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time','>','0');
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        return $getlist;
    }
}