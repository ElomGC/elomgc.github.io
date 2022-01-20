<?php
declare(strict_types = 1);

namespace app\common\model\form;

use app\common\model\R;
use app\facade\wormview\Upload;
use think\facade\Db;

class FormModel extends R {
    protected $schema = [
        'id'          => 'int',
        'title'        => 'string',
        'cont'        => 'text',
        'see_group'        => 'string',
        'add_group'        => 'string',
        'tourist'      => 'tinyint',
        'status'      => 'tinyint',
        'addtime'      => 'int',
        'del_time'      => 'int',
        'sort'      => 'int',
    ];
    //  获取模型列表
    public function getList($data = []){
        $data['status'] = !isset($data['status']) ? 'a' : $data['status'];
        $data['del_time'] = empty($data['del_time']) ? '0' : $data['del_time'];
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
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
            foreach ($getlist as $k => $v){
                $v['article_num'] = Db::name('form_content_'.$v['id'])->whereDelTime('0')->count();
                $getlist[$k] = $v;
            }
        }
        return $getlist;
    }
    public function getEditAdd($data = [])
    {
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
            'oldimg' => empty($old['cont']) ? null : Upload::editadd($old['cont'],false),
            'newimg' => $_data['cont'],
        ];
        $_data['cont'] = Upload::setEditOr($img_data, 'form/'.date('Y-m', time()));
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        $_data['del_time'] = empty($old['del_time']) ? '0' : $old['del_time'];
        $_data['see_group'] = empty($_data['see_group']) ? null : implode(',',$_data['see_group']);
        $_data['add_group'] = empty($_data['add_group']) ? null : implode(',',$_data['add_group']);
        $_data = Upload::editadd($_data);
        return $_data;
    }
    public function getOne($id)
    {
        $getlist = $this->getList(['id' => $id,'status' => 'a']);
        return empty($getlist['0']) ? [] : $getlist['0'];
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
        return $res;
    }
    //  生成数据表
    protected function addNewTabel($mid,$name){
        $tabelname = config('database.connections.mysql.prefix').'form_content_'.$mid;
        $result = Db::query("SHOW TABLES LIKE '{$tabelname}'");
        if(!empty($result)){
            return false;
        }
        $file_list = "CREATE TABLE IF NOT EXISTS `" . $tabelname . "` (
                        `id` mediumint(8) NOT NULL auto_increment,
                        `mid` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
                        `uid` varchar(100) NOT NULL COMMENT '用户ID',
                        `title` varchar(255) NOT NULL COMMENT '标题',
                        `content` text(0) DEFAULT NULL COMMENT '内容',
                        `res_content` text(0) DEFAULT NULL COMMENT '回复内容',
                        `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核 0 =》 待审核，1 =》 通过，2=》拒绝|一般用不上',
                        `jian` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐,0=>不推荐，1=>推荐,2以上推荐为分类级以上推荐',
                        `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
                        `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
                        `addip` varchar(20) NOT NULL COMMENT '添加IP',
                        `del_time` int(11)  NOT NULL DEFAULT '0',
                        PRIMARY KEY  (`id`),
                        KEY `mid` (`mid`),
                        KEY `uid` (`uid`),
                        KEY `jian` (`jian`),
                        KEY `addtime` (`addtime`)
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
        Db::name('form_filed')->insertAll($file_list);
        return true;
    }
}