<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use think\Model;
use worm\NodeFormat;

abstract class PartBase extends Model {
    protected $table;
    protected $name;
  //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    protected $schema = [
        "id" => "int",
        "title" => "string",
        "class" => "tinyint",
        "pid" => "int",
        "mid" => "int",
        "limit" => "int",
        "title_num" => "int",
        "cont_num" => "int",
        "group_uid" => "string",
        "group_see" => "string",
        "group_edit" => "string",
        "pid_see" => "tinyint",
        "comment_see" => "tinyint",
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
    protected $type = [
        'addtime'  =>  'timestamp',
        'del_time'  =>  'timestamp',
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
        self::$base_table = $array['0']['3']."_content";
        self::$model_key = $array['0']['3'];
        self::$table_prefix = config('database.connections.mysql.prefix');
        $this->table = self::$table_prefix.self::$model_key."_part";
        $this->name = self::$model_key."_part";
    }

    /**
     * 获取栏目
     * @param array $data  getdeltime 仅限于回收站使用
     * @return array
     */
    public function getList($data = []){
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
        $map = !empty($data['mid']) ? $map->whereIn('mid',$data['mid']) : $map;
        $map = !empty($data['group_edit']) ? $map->whereRaw('find_in_set(:groupid,group_edit) or `group_edit` is null or LENGTH(trim(group_edit)) = 0',['groupid' => $data['group_edit']]) : $map;
        $map = !empty($data['group_see']) ? $map->whereRaw('find_in_set(:groupid,group_see) or `group_see` is null or LENGTH(trim(group_see)) = 0',['groupid' => $data['group_see']]) : $map;
        $map = !empty($data['group_uid']) ? $map->whereRaw('find_in_set(:groupid,group_uid) or `group_uid` is null or LENGTH(trim(group_uid)) = 0',['groupid' => $data['group_uid']]) : $map;
        $map = isset($data['class']) && $data['class'] != 'a' ? $map->whereClass($data['class']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;

        $getlist = $map->order('sort desc,id asc')->select()->toArray();
        if(!empty($getlist)){
            $fids = array_column($getlist,'id');
            $fids = array_count_values(Db::name(self::$base_table)->whereDelTime('0')->whereIn('fid',$fids)->column('fid'));
            foreach ($getlist as $k => $v){
                $_v = empty($fids[$v['id']]) ? '0' : $fids[$v['id']];
                $v['article_num'] = $v['class'] == '1' ? '' : $_v;
                $getlist[$k] = $v;
            }
        }
        return $getlist;
    }
    public function getOne($id){
        $getdata = $this->getList(['id' => $id,'status' => 'a']);
        return empty($getdata['0']) ? [] : $getdata['0'];
    }
    //  数据清洗
    public function EditAdd($data){
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
        $img_data = [
            'oldimg' => empty($old['description']) ? null : Upload::editadd($old['description'],false),
            'newimg' => $_data['description'],
        ];
        $_data['description'] = Upload::setEditOr($img_data, 'part/'.date('Y-m', time()));
        $_data['logo'] = $this->setFile(empty($_data['logo']) ? null : $_data['logo'],empty($old['logo']) ? null : $old['logo']);
        $_data['banber'] = $this->setFile(empty($_data['banber']) ? null : $_data['banber'],empty($old['banber']) ? null : $old['banber']);
        $_data['addtime'] = empty($old['addtime']) ? time() : strtotime($old['addtime']);
        $_data['del_time'] = empty($old['del_time']) ? '0' : strtotime($old['addtime']);
        $_data['group_see'] = empty($_data['group_see']) ? null : implode(',',$_data['group_see']);
        $_data['group_edit'] = empty($_data['group_edit']) ? null : implode(',',$_data['group_edit']);
        $_data = Upload::editadd($_data);
        return $_data;
    }
    //  保存数据
    public function setOne($data){
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
    //  处理文件
    protected function setFile($new,$old){
        $old = empty($old) ? null : Upload::editadd($old,false);
        if($new != $old){
            if(!empty($new)){
                $new = Upload::fileMove($new);
            }
            if(!empty($old)){
                Upload::fileDel($old);
            }
        }
        return $new;
    }
    public function DeleteOne($id) {
        $id = is_array($id) ? $id : (int) $id;
        $_oldlist = $this->getList(['getdeltime' => '1','status' => 'a']);
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
        if(is_array($id)){
            $_deltime = array_column($_oldlist,'del_time');
            $old['del_time'] = time();
            if(count(Common::del_null($_deltime)) < count($_deltime)){
                $old['del_time'] = '0';
            }
        }else{
            $old = Common::del_file($_oldlist,'id',$id);
            $old = $old['0'];
        }
        if(empty($old['del_time'])){
            if(!$this->whereIn('id',$_oldid)->update(['status' => '0','del_time' => time()])){
                return false;
            }
        }else{
            if(array_sum(array_column($_oldlist,'article_num')) > 0){
                return ['code' => '0','msg' => '栏目存在内容，禁止删除'];
            }
            $del_file = array_merge(array_column($_oldlist,'banber'), array_column($_oldlist,'logo'));
            Upload::fileDel(Upload::editadd(Common::del_null($del_file),false));
            $del_file = Upload::editadd(Common::del_null(array_column($_oldlist,'description')),false);
            if(!empty($del_file)){
                foreach ($del_file as $k => $v){
                    $v = Upload::img_list($v);
                    Upload::fileDel($v);
                    continue;
                }
            }
            if(!$this->whereIn('id',$_oldid)->delete()){
                return false;
            }
        }
        return true;
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
    //  格式化栏目列表
    public function getPartList($data = []){
        $getlist = $this->getList($data);
        $getlist = Upload::editadd($getlist,false);
        foreach ($getlist as $k => $v){
            unset($v['limit'],$v['title_num'],$v['cont_num'],$v['group_uid'],$v['group_see'],$v['group_edit'],$v['comment_see'],$v['status'],$v['keywords'],$v['description'],$v['password'],$v['temp_late'],$v['temp_head'],$v['temp_list'],$v['temp_cont'],$v['temp_foot'],$v['sort'],$v['order'],$v['article_num'],$v['level']);
            $getlist[$k] = $v;
        }
        $getlist = NodeFormat::toList($getlist);
        return $getlist;
    }
}