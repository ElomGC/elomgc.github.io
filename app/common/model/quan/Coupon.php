<?php
declare(strict_types = 1);

namespace app\common\model\quan;

use app\common\model\R;
use app\facade\hook\Common;
use think\facade\Db;

class Coupon extends R
{
    protected $table;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'        => 'string',
        'zonenum'        => 'int',
        'class'        => 'int',
        'class_type'        => 'int',
        'type'        => 'int',
        'minmoney'        => 'string',
        'group'        => 'text',
        'onelimit'        => 'int',
        'time_type'        => 'int',
        'add_time'        => 'int',
        'end_time'        => 'int',
        'time_num'        => 'int',
        'model_limit'        => 'int',
        'model_list'        => 'text',
        'article_limit'        => 'int',
        'article_list'        => 'text',
        'condition'        => 'text',
        'status'        => 'int',
        'sort'        => 'int',
        'addtime'        => 'int',
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
        $this->base_table = $array['0']['3']."_coupon";
        $this->model_key = $array['0']['3'];
        $this->table_prefix = config('database.connections.mysql.prefix');
        $this->table = $this->table_prefix.$this->model_key."_coupon";
    }
    public function getEditAdd($data = [])
    {
        $_data = parent::getEditAdd($data);
        $_data['minmoney'] = $_data['type'] == '1' ? $_data['minmoney'] : '0';
        $_data['time_num'] = $_data['time_type'] == '1' ? $_data['time_num'] : '0';
        $_data['add_time'] = $_data['time_type'] == '1' ? '0' : strtotime($_data['add_time']);
        $_data['end_time'] = $_data['time_type'] == '1' ? '0' : strtotime($_data['end_time']);
        $_data['model_list'] = $_data['model_limit'] == '0' ? null :  implode(',',$_data['model_list']);;
        $_data['article_list'] = $_data['article_limit'] == '0' ? null : $_data['article_list'];
        $_data['group'] = empty($_data['group']) ? null : implode(',',$_data['group']);
        return $_data;
    }

    public function getList($data = []){
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time', '>', '0');
        if(!empty($data['model_list'])){
            $data['model_list'] = is_array($data['model_list']) ? $data['model_list'] : explode(',',$data['model_list']);
            $data['model_list'] = Common::del_null($data['model_list']);
            $_raw[] = "`model_list` is null or `model_list` = ''";
            foreach ($data['model_list'] as $k => $v){
                $_raw[] = "FIND_IN_SET('{$v}', `model_list`)";
            }
            $_raw = implode(' or ',$_raw);
            $map = $map->whereRaw($_raw);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if($data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('sort desc,id asc')->paginate($limit);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $_cid = array_column($getlist['data'], 'id');
            $_cid = Db::name($this->base_table.'_user')->whereIn('cid',$_cid)->field('id,cid')->select()->toArray();
            foreach ($getlist['data'] as $k => $v){
                $_v = Common::del_file($_cid,'cid',$v['id']);
                $v['add_num'] = count($_v);
                $v['zone_num'] = empty($v['zonenum']) ? $v['add_num'] : $v['zonenum'] - $v['add_num'];
                $_v = Common::del_file($_v,'status','1');
                $v['end_num'] = count($_v);
                $v['add_time'] = empty($v['add_time']) ? '' : date('Y-m-d',$v['add_time']);
                $v['end_time'] = empty($v['end_time']) ? '' : date('Y-m-d',$v['end_time']);
                $getlist['data'][$k] = $v;
            }
        }
        $getlist['page'] = $page;
        return $getlist;
    }
    //  生成优惠券
    public function generate($data){
        $_id = "YS".date('YmdHis',time()).get_range(8);
        if($data['time_type'] == '0'){
            $_addtime = strtotime($data['add_time']);
            $_endtime = strtotime($data['end_time']);
        } else if($data['time_type'] == '1'){
            $_addtime = time();
            $_endtime = $_addtime + (86400 * $data['time_num']);
        }
        $add = [
            'id' => $_id,
            'cid' => $data['id'],
            'add_time' => $_addtime,
            'end_time' => $_endtime,
            'status' => '0',
        ];
        return $add;
    }
}