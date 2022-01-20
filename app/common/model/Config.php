<?php
declare(strict_types = 1);
namespace app\common\model;

use app\facade\hook\Common;
use think\Model;
use think\model\concern\SoftDelete;

class Config extends R{
    use SoftDelete;
    protected $deleteTime = 'del_time';
    protected $defaultSoftDelete = 0;
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'        => 'string',
        'conf'        => 'string',
        'conf_value'        => 'text',
        'class'        => 'string',
        'form_type'        => 'string',
        'form_required'      => 'tinyint',
        'form_required_list'      => 'string',
        'form_text'      => 'string',
        'form_unit'      => 'string',
        'form_default'      => 'string',
        'form_data'        => 'text',
        'form_tip'      => 'string',
        'status'      => 'tinyint',
        'sort'      => 'int',
        'del_time'      => 'int',
    ];
    //
    public function getConfedit($data){
        $getlist = $this->whereClass($data['class'])->whereStatus('1')->order('sort desc,id asc')->select()->toArray();
        if(empty($getlist)){
            return false;
        }
        foreach ($getlist as $k => $v){
            $v['form_title'] = $v['title'];
            $v['sql_file'] = $v['conf'];
            $v['default'] = empty($v['conf_value']) ? $v['form_default'] : $v['conf_value'];
            $getlist[$k] = $v;
        }
        return $getlist;
    }
    public function getConfigClass(){
        $model = new ConfigClass();
        $getList = $model->order('sort desc,id asc')->select()->toArray();
        $new_getlist = array();
        if(empty($getList)){
            return $new_getlist;
        }
        foreach ($getList as $k => $v){
            $new_getlist[] = array(
                'id' => $v['id'],
                'title' => $v['title'],
                'icon' => $v['icon'],
                'uri' =>  empty($v['uri']) ? url('config/confedit',['class' => $v['id']])->build() : $v['uri'],
            );
        }
        return $new_getlist;
    }
    public function getList($data = [])
    {
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['class']) ? $map->whereIn('class',is_array($data['class']) ? $data['class'] : (string) $data['class']) : $map;
        $map = !empty($data['title']) ? $map->whereIn('title',$data['title']) : $map;
        $map = !empty($data['conf']) ? $map->whereIn('conf',$data['conf']) : $map;
        $map = !empty($data['key']) ? $map->whereLike('title|conf',"%{$data['key']}%") : $map;
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('sort desc,id asc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        return $getlist;
    }
}