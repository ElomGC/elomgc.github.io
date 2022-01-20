<?php
declare(strict_types = 1);

namespace app\common\model\shop;

use app\common\model\R;

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
        $_data['article_list'] = $_data['article_limit'] == '0' ? '' : $_data['article_list'];
        $_data['group'] = empty($_data['group']) ? '' : implode(',',$_data['group']);;
        return $_data;
    }


}