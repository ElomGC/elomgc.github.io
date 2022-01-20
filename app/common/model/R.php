<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use think\facade\Request;
use think\Model;

abstract class R extends Model
{
    protected $table;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    //  清洗数据
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
        $old = empty($old) ? [] : $old->toArray();
        if(empty($old)){
            unset($_data['id']);
        }
        if(in_array('addtime',$fileList)){
            $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        }
        if(in_array('addip',$fileList)){
            $_data['addip'] = empty($old['addip']) ? Request::ip() : $old['addip'];
        }
        return $_data;
    }
    //  保存数据
    public function setOne($data){
        if(empty($data['id'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    public function getList($data = []){
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $this->order('id desc')->paginate($limit);
        $getlist = Common::JobArray($getlist);
        return $getlist;
    }
    public function getOne($id){
        $getlist = $this->getList([$this->pk => $id,'page' => '1']);
        return $getlist['total'] > '0' ? $getlist['data']['0'] : [];
    }
}