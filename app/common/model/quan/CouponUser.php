<?php
declare(strict_types = 1);

namespace app\common\model\quan;


use app\common\model\R;
use app\facade\hook\Common;

class CouponUser extends R
{
    protected $table;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    // 设置字段信息
    protected $schema = [
        'id'          => 'varchar',
        'oid'          => 'varchar',
        'cid'        => 'int',
        'uid'        => 'int',
        'add_time'        => 'int',
        'end_time'        => 'int',
        'status'        => 'int',
        'del_time'        => 'int',
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
        $this->base_table = $array['0']['3']."_coupon_user";
        $this->model_key = $array['0']['3'];
        $this->table_prefix = config('database.connections.mysql.prefix');
        $this->table = $this->table_prefix.$this->model_key."_coupon_user";
    }
    public function getList($data = [],$uns = true){
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['cid']) ? $map->whereIn('cid',is_array($data['cid']) ? $data['cid'] : (string) $data['cid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        if(isset($data['status']) && $data['status'] != 'a') {
            $map = $map->whereStatus($data['status']);
        }else if(empty($data['status'])){
            $map = $map->whereStatus('0');
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('addtime desc')->paginate($limit);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        if($uns){
            $_cids = array_unique(array_column($getlist['data'],'cid'));
            $cModel = "app\\common\\model\\" . $this->model_key . "\\Coupon";
            $cModel = new $cModel;
            $_cids = $cModel->getList(['id' => $_cids,'status' => 'a','limit' => count($_cids)]);
            $_cids = $_cids['data'];
            foreach ($getlist['data'] as $k => $v){
                $_v = Common::del_file($_cids,'id',$v['cid']);
                $v['title'] = empty($_v['0']['title']) ? '-' : $_v['0']['title'];
                $v['base'] = [];
                if(!empty($_v['0'])){
                    unset($_v['0']['id']);
                    $v['base'] = $_v['0'];
                }
                $v['add_times'] = empty($v['add_time']) ? '0' : date('Y-m-d H:i:s',$v['add_time']);
                $v['end_times'] = empty($v['end_time']) ? '0' : date('Y-m-d H:i:s',$v['end_time']);
                $getlist['data'][$k] = $v;
            }
        }
        $getlist['page'] = $page;
        return $getlist;
    }
    public function setOne($data)
    {
        $_old = $this->getOne($data['id']);
        if(empty($_old)){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->whereId($data['id'])->update($data)){
                return false;
            }
        }
        $res = $this->getOne($data['id']);
        return $res;
    }

    /**
     * @param $_money 订单总金额
     * @param $id 优惠券ID
     * @param $_shoplist    订单商品列表
     * @param $uid  用户UID
     */
    public function CountPay($_money,$id,$_shoplist,$uid):array {
        $_coupuser = $this->getOne($id);
        $res['code'] = '0';
        $res['data'] = '';
        if($_coupuser['uid'] != $uid){
            $res['msg'] = '你没有此优惠券';
        }
        if($_coupuser['status'] != '0'){
            $res['msg'] = '优惠券已使用';
        }
        if($_coupuser['add_time'] > time()){
            $res['msg'] = '优惠券未到使用时间';
        }
        if($_coupuser['end_time'] < time()){
            $res['msg'] = '优惠券已过期';
        }
        $_coup = $_coupuser['base'];
        if($_coup['model_limit'] == '1'){
            $_model_list = explode(',',$_coup['model_list']);
            $_cshoplist = Common::del_file($_shoplist,'model',$_model_list);
            if(count($_cshoplist) > '0'){
                if($_coup['article_limit'] == '1'){
                    $_model_list = explode(',',$_coup['article_list']);
                    $_cshoplist = Common::del_file($_cshoplist,'aid',$_model_list);
                    if(count($_cshoplist) > '0'){
                        if($_coup['type'] == '1') {
                            $_model_list = [];
                            foreach ($_cshoplist as $k => $v){
                                $_v = $v['money'] / 100;
                                if($_v < $_coup['minmoney']){
                                    unset($_cshoplist[$k]);
                                }
                                $_model_list = $v;
                            }
                            if(!empty($_model_list)){
                                $res['data'] = $_coup['class'] == '0' ? $_model_list['money_zon'] * (100 - $_coup['class_type']) / 10000 : $_coup['class_type'];
                            }
                        }else {
                            $_model_list = Common::ArraySort($_cshoplist,'money_zon','desc');
                            $res['data'] = $this->EditModel($_coup, $_model_list['0']['money_zon']);
                        }
                    }
                }else{
                    $_cshoplist = array_column($_cshoplist,'money_zon');
                    $res['data'] = $this->EditModel($_coup,array_sum($_cshoplist));
                }
            }
        }else{
            $res['data'] = $this->EditModel($_coup,$_money);
        }
        if(!empty($res['data'])){
            $res['code'] = '1';
            $res['msg'] = '查询成功';
        }
        return $res;
    }

    protected function EditModel($base,$money){
        if($base['type'] == '1' && ($money / 100) > $base['minmoney']){
            if($base['class'] == '0') {
                $_del = $money * (100 - $base['class_type']) / 10000;
            }else{
                $_del = $base['class_type'];
            }
        }else if($base['type'] == '0'){
            if($base['class'] == '0') {
                $_del = $money * (100 - $base['class_type']) / 10000;
            }else{
                $_del = $base['class_type'];
            }
        }
        return $_del;
    }
}