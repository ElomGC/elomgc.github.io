<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;

class FupartArticle extends R
{
    protected $table;
    protected $name;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    protected $schema = [
        "id" => "int",
        "status" => "tinyint",
        'fuid'      => 'int',
        'aid'      => 'int',
        'del_time'      => 'int',
    ];
    /**
     * 初始化模型信息
     */
    protected $base_table;
    protected $model_key;
    protected $table_prefix;

    protected function initialize():void{
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $this->base_table = $array['0']['3']."_content";
        $this->model_key = $array['0']['3'];
        $this->table_prefix = config('database.connections.mysql.prefix');
        $this->table = $this->table_prefix.$this->model_key."_fupart_article";
        $this->name = $this->model_key."_fupart_article";
    }
    public function setOne($data)
    {
        $fuid = array_merge([],array_unique(array_column($data,'fuid')));
        $aid = array_merge([],array_unique(array_column($data,'aid')));
        $res = $this->whereIn('fuid',$fuid)->whereIn('aid',$aid)->select()->toArray();
        if(empty($res)){
            $this->saveAll($data);
        }else{
            $_data = $data;
            foreach ($res as $k => $v){
                foreach ($data as $k1 => $v1){
                    if($v['fuid'] == $v1['fuid'] && $v['aid'] == $v1['aid']){
                        unset($data[$k1]);
                    }
                }
            }
            if(!empty($data)){
                $this->saveAll($data);
            }
            $res = $this->whereIn('fuid',$fuid)->whereIn('aid',$aid)->select()->toArray();
            foreach ($res as $k => $v){
                $_v = Common::del_file($_data,'fuid',$v['fuid']);
                $_v = !empty($_v) ?Common::del_file($_v,'aid',$v['aid']) : [];
                if(!empty($_v)){
                    unset($res[$k]);
                }
                continue;
            }
            if(!empty($res)){
                $_id = array_column($res,'id');
                $this->whereIn('id',$_id)->delete();
            }
        }
        return true;
    }
    public function getList($data = [])
    {
        $map = $this;
        $map = !empty($data['fuid']) ? $map->whereIn('fuid',is_array($data['fuid']) ? $data['fuid'] : (string) $data['fuid']) : $map;
        $map = !empty($data['aid']) ? $map->whereIn('aid',is_array($data['aid']) ? $data['aid'] : (string) $data['aid']) : $map;
        if(empty($data['status'])){
            $map = $map->whereStatus('1');
        }else if(!isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('id desc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $aModel = "app\\common\\model\\{$this->model_key}\\Article";
            $aModel = new $aModel;
            $_getlist = array_unique(array_column($getlist['data'],'aid'));
            $_getlist = $aModel->getList(['id' => $_getlist,'page' => '1','limit' => count($_getlist)]);
            $_getlist = $_getlist['data'];

            foreach ($getlist['data'] as $k => $v){
                $_v = Common::del_file($_getlist,'id',$v['aid']);
                $_v = $_v['0'];
                unset($_v['id']);
                $_special_name = Common::del_file($_v['fupart'],'fuid',$v['fuid']);
                $_v['fupart_name'] = $_special_name['0']['fupart_name'];
                $getlist['data'][$k] = array_merge($_v,$v);
            }
        }
        return $getlist;
    }
    public function DeleteOne($ids){
        if($this->whereIn('id',is_array($ids) ? $ids : (string) $ids)->delete()){
            return true;
        }
        return false;
    }
}