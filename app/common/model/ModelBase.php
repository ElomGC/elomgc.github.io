<?php
declare(strict_types = 1);

namespace app\common\model;
use think\facade\Db;
use think\Model;
abstract class ModelBase extends Model {
    protected $table;
  //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    protected $schema = [
        'id'          => 'int',
        'title'        => 'string',
        'futitle'        => 'string',
        'see_group'        => 'string',
        'edit_group'        => 'string',
        'status'      => 'tinyint',
        'see_picurl'      => 'tinyint',
        'see_keyword'      => 'tinyint',
        'see_description'      => 'tinyint',
        'see_comment'      => 'tinyint',
        'see_add'      => 'tinyint',
        'order'      => 'tinyint',
        'order_group'      => 'tinyint',
        'order_money'      => 'varchar',
        'addtime'      => 'int',
        'del_time'      => 'int',
        'sort'      => 'int',
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
        $this->table = self::$table_prefix.self::$model_key."_model";
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
        $_data['addtime'] = empty($old['addtime']) ? time() : strtotime($old['addtime']);
        $_data['del_time'] = empty($old['del_time']) ? '0' : strtotime($old['del_time']);
        $_data['order_money'] = empty($_data['order_money']) ? '' : implode(',',$_data['order_money']);
        $_data['see_group'] = empty($_data['see_group']) ? '' : implode(',',$_data['see_group']);
        $_data['edit_group'] = empty($_data['edit_group']) ? '' : implode(',',$_data['edit_group']);
        return $_data;
    }
    //  保存数据
    public function setOne($data){
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
            if(!$this->addNewTabel($add->id,$add->title)){
                $add->delete();
                return false;
            }
        }else{
            if(!$this->whereId($data['id'])->save($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        //  生成订单表
        if($res['order'] == '1'){
            $tabelname = self::$table_prefix.'order_'.self::$model_key;
            $result = Db::query("SHOW TABLES LIKE '{$tabelname}'");
            if(empty($result)){
                $this->addOrderTabel($tabelname);
            }
        }
        return $res;
    }
    //  生成订单表
    protected function addOrderTabel($name){
        $basename = self::$table_prefix.'order';
        $array = Db::query("SHOW CREATE TABLE {$basename}")[0];
        $array['Create Table'] = str_replace($basename,$name,$array['Create Table']);
        Db::execute($array['Create Table']);
        return true;
    }
    //  生成数据表
    protected function addNewTabel($mid,$name){
        $tabelname = self::$table_prefix.self::$base_table.'_'.$mid;
        $result = Db::query("SHOW TABLES LIKE '{$tabelname}'");
        if(!empty($result)){
            return false;
        }
        $file_list = "CREATE TABLE IF NOT EXISTS `" . $tabelname . "` (
                        `id` mediumint(8) NOT NULL auto_increment,
                        `mid` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
                        `fid` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
                        `uid` varchar(100) NOT NULL COMMENT '用户ID',
                        `title` varchar(255) NOT NULL COMMENT '标题',
                        `content` text(200) DEFAULT NULL COMMENT '内容',
                        `keywords` varchar(50) DEFAULT NULL COMMENT '关键词',
                        `description` varchar(255) DEFAULT NULL COMMENT '内容简介',
                        `picurl` varchar(200) DEFAULT NULL COMMENT '缩略图',
                        `comment_num` int(11) NOT NULL DEFAULT '0' COMMENT '评论量',
                        `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核 0 =》 未审，1 =》 已审，2=》拒绝|一般用不上',
                        `hist` int(11) NOT NULL DEFAULT '0' COMMENT '点击',
                        `jian` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐,0=>不推荐，1=>推荐,2以上推荐为分类级以上推荐',
                        `zan` int(11) NOT NULL DEFAULT '0' COMMENT '点赞',
                        `zhuan` int(11) NOT NULL DEFAULT '0' COMMENT '转发',
                        `addtime` int(11) NOT NULL COMMENT '添加时间',
                        `sort` int(11) NOT NULL COMMENT '排序',
                        `edittime` int(11) DEFAULT '0' COMMENT '修改时间',
                        `addip` varchar(20) NOT NULL COMMENT '添加IP',
                        `editip` varchar(20) DEFAULT NULL COMMENT '修改IP',
                        `del_time` int(11)  NOT NULL DEFAULT '0',
                        `jumpurl` varchar(255) DEFAULT NULL COMMENT '跳转链接',
                        PRIMARY KEY  (`id`),
                        KEY `fid` (`fid`),
                        KEY `mid` (`mid`),
                        KEY `uid` (`uid`),
                        KEY `hist` (`hist`),
                        KEY `comment_num` (`comment_num`),
                        KEY `jian` (`jian`),
                        KEY `zan` (`zan`),
                        KEY `zhuan` (`zhuan`),
                        KEY `addtime` (`addtime`),
                        KEY `edittime` (`edittime`)
                   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='{$name}模型表' AUTO_INCREMENT=1;";
        try {
            Db::execute($file_list);
        } catch(\Exception $e) {
            return false;
        }
        $file_list = [
            ['mid'        => $mid,
            'sql_file'      => 'title',
            'sql_type'      => "varchar(255) DEFAULT NULL",
            'form_title'    => '标题',
            'form_text'    => null,
            'form_required'    => '1',
            'form_required_list'    => null,
            'form_unit'    => null,
            'form_default'       => null,
            'form_type'        => 'text',
            'form_class'        => null,
            'form_data'        => null,
            'form_tip'        => null,
            'form_group'        => null,
            'form_js'        => null,
            'group_see'        => null,
            'group_edit'        => null,
            'status'        => '1',
            'sort'        => '100',
            'list_show'        => '1',
            'cont_show'        => '1',
            'addtime'        => time(),
            'del_time'        => '0',],['mid'        => $mid,
            'sql_file'      => 'content',
            'sql_type'      => 'mediumtext DEFAULT NULL',
            'form_title'    => '内容',
            'form_text'    => null,
            'form_required'    => '0',
            'form_required_list'    => null,
            'form_unit'    => null,
            'form_default'       => null,
            'form_type'        => 'editor',
            'form_class'        => null,
            'form_data'        => null,
            'form_tip'        => null,
            'form_group'        => null,
            'form_js'        => null,
            'group_see'        => null,
            'group_edit'        => null,
            'status'        => '1',
            'sort'        => '99',
            'list_show'        => '1',
            'cont_show'        => '1',
            'addtime'        => time(),
            'del_time'        => '0',],
        ];
        Db::name(self::$model_key.'_filed')->insertAll($file_list);
        return true;
    }
    //  获取模型列表
    public function getList($data = []){
        $data['status'] = !isset($data['status']) ? 'a' : $data['status'];
        $data['del_time'] = empty($data['del_time']) ? '0' : $data['del_time'];
        $map = $this;
        if(!empty($data['id'])){
            $map = $map->whereId($data['id']);
        }
        $map = isset($data['see_add']) ? $map->whereSeeAdd($data['see_add']) : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if(empty($data['getdeltime'])) {
            if (empty($data['del_time'])) {
                $map = $map->whereDelTime('0');
            } else {
                $map = $map->where('del_time', '>', '0');
            }
        }
        $getlist = $map->order('id asc')->select()->toArray();
        if(app('http')->getName() == 'admin' || (!empty($data['articlenum']) && $data['articlenum'] == 'get')){
            $fids = array_column($getlist,'id');
            $fids = array_count_values(Db::name(self::$base_table)->whereDelTime('0')->whereIn('mid',$fids)->column('mid'));
            foreach ($getlist as $k => $v){
                $v['article_num'] = empty($fids[$v['id']]) ? '0' : $fids[$v['id']];
                $getlist[$k] = $v;
            }
        }
        if(app('http')->getName() == 'admin' || (!empty($data['partnum']) && $data['partnum'] == 'get')){
            $fids = array_column($getlist,'id');
            $fids = array_count_values(Db::name(self::$model_key.'_part')->whereDelTime('0')->whereIn('mid',$fids)->column('mid'));
            foreach ($getlist as $k => $v){
                $v['part_num'] = empty($fids[$v['id']]) ? '0' : $fids[$v['id']];
                $getlist[$k] = $v;
            }
        }
        return $getlist;
    }
    public function DeleteOne($id) {
        if($id == '1'){
            return ['code' => '0','msg' => '基础模型禁止删除'];
        }
        $old = $this->whereId($id)->find();
        if(empty($old['del_time'])){
            if(!$this->whereId($id)->update(['status' => '0','del_time' => time()])){
                return false;
            }
        }else{
            if(Db::name(self::$base_table)->whereMid($id)->count() > '0'){
                return ['code' => '0','msg' => '模型存在内容，禁止删除'];
            }
            if(Db::name(self::$model_key.'_part')->whereMid($id)->count() > '0'){
                return ['code' => '0','msg' => '模型存在栏目，禁止删除'];
            }
            //  删除模型所有字段
            Db::name(self::$model_key.'_filed')->whereMid($id)->delete();
            $old->delete();
        }
        return true;
    }
}