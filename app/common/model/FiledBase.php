<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;use think\Model;

abstract class FiledBase extends Model {
    protected $table;
  //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

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
        "form_default" => "varchar",
        "form_type" => "string",
        "form_class" => "string",
        "form_geturi" => "string",
        "form_geturitype" => "int",
        "form_edit" => "int",
        "form_data" => "text",
        "form_tip" => "string",
        "form_group" => "string",
        "form_js" => "text",
        "group_see" => 'string',
        "group_edit" => "string",
        "setstatus" => "tinyint",
        "status" => "tinyint",
        "sort" => "int",
        "admin_list_show" => "tinyint",
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
    /**
    * 初始化模型信息
     */
    protected static $base_table;
    protected static $model_key;
    protected static $table_prefix;
    protected function initialize():void{
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$base_table = $array['0']['3']."_content";
        self::$model_key = $array['0']['3'];
        self::$table_prefix = config('database.connections.mysql.prefix');
        $this->table = self::$table_prefix.self::$model_key."_filed";
    }
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
        $_data['addtime'] =empty($old['addtime']) ? time() : strtotime($old['addtime']);
        $_data['del_time'] =empty($old['del_time']) ? '0' : strtotime($old['del_time']);
        $_data['group_see'] = empty($_data['group_see']) ? null : implode(',',$_data['group_see']);
        $_data['group_edit'] = empty($_data['group_edit']) ? null : implode(',',$_data['group_edit']);
        return $_data;
    }
    //  检测字段是否存在
    public function checkFiled($data){
        $file_list = Db::name(self::$base_table."_{$data['mid']}")->getTableFields();
        if(empty($data['sql_file']) || in_array($data['sql_file'],$file_list)){
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
            if(!$this->whereId($old['id'])->update($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  添加数据表字段
    public function addTabelFiled($data){
        $tabelname = self::$table_prefix.self::$base_table.'_'.$data['mid'];
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
    //  删除数据表字段
    public function DeleteOne($id){
        $old = $this->whereId($id)->find();
        if(in_array($old['sql_file'],['title','content'])){
            return ['code' => '0','msg' => '此字段禁止删除'];
        }
        $tablename = self::$table_prefix.self::$base_table.'_'.$old['mid'];
        if(in_array($old['form_type'],["editor","upload_img","upload_file","upload_imgtc","upload_imgarr","upload_imgarrtc","upload_filearr"])){
            $filed_list = Db::table($tablename)->whereNotNull($old['sql_file'])->column($old['sql_file']);
            if(!empty($filed_list)){
                //  删除文件处理
                foreach ($filed_list as $k => $v){
                    if(empty($v)){
                        continue;
                    }
                    if(in_array($old['form_type'],["upload_img","upload_file","upload_imgtc","upload_imgarr","upload_imgarrtc","upload_filearr"])){
                        Upload::fileDel(Upload::editadd(array_column(json_decode($v,true),'uri'),false));
                    }else{
                        Upload::fileDel(Upload::editadd(Upload::img_list($v),false));
                    }
                }
            }
        }
        Db::execute("alter table `{$tablename}` drop `{$old['sql_file']}`");
        $old->delete();
        return true;
    }
    //  获取所有字段
    public function getList($data){
        $map = $this;
        $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time','>','0');
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $map = !empty($data['getField']) ? $map->field($data['getField']) : $map;
        $map = !empty($data['mid']) ? $map->whereIn('mid',is_array($data['mid']) ? $data['mid'] : (string) $data['mid']) : $map;
        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        return $getlist;
    }
    //  格式化表单
    public function ReadFile($data){
        $new_data = ['file' => $data['sql_file'],'title' => $data['form_title'],'title_edit' => $data['form_text'],'type' => $data['form_type'],'type_group' => $data['form_group'],'required' => $data['form_required'] == '1' ? true : false,'type_unit' => $data['form_unit'],'tip' => $data['form_tip']];
        if(in_array($data['form_type'],['radio','checkbox','select'])){
            $arr = Common::data_trim(explode("\r\n",str_replace(array("\r\n", "\r", "\n"), "\r\n", $data['form_data'])));
            $_arr = [];
            foreach ($arr as  $k => $v){
                $v = explode('|',$v);
                $v['1'] = empty($v['1']) ? $v['0'] : $v['1'];
                $_arr[$v['0']] = $v['1'];
            }
            $new_data['data'] = ['list' => $_arr,'default' => $data['from_default']];
        }else if(in_array($data['form_type'],['upload_img','upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr'])){
            if(in_array($data['form_type'],['upload_file','upload_filearr'])){
                $new_data['upload_accept'] = 'file';
            }
            if(in_array($data['form_type'],['upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr'])){
                $new_data['upload_filenum'] = '9';
            }else{
                $new_data['upload_filenum'] = '1';
            }
            if(in_array($data['form_type'],['upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr'])){
                $new_data['type_edit'] = 'array';
            }
            $new_data['type'] = 'upload';
        }else if(in_array($data['form_type'],['number','money','time','date','datetime','hidden'])){
            $new_data['type'] = 'text';
            if($data['form_type'] == 'number'){
                $new_data['type_edit'] = 'number';
            }else if($data['form_type'] == 'money'){
                $new_data['type_unit'] = !empty($new_data['type_unit']) ? $new_data['type_unit'] : '元';
            }else if($data['form_type'] == 'time'){
                $new_data['type_edit'] = 'time';
            }else if($data['form_type'] == 'datetime'){
                $new_data['type_edit'] = 'datetime';
            }else if($data['form_type'] == 'date'){
                $new_data['type_edit'] = 'date';
            }else{
                 $new_data['type_edit'] = 'hidden';
            }
        }
        return $new_data;
    }
}