<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;

class PayBase extends R
{
    protected $pk = 'oid';

    protected $schema = [
        "oid" => 'varchar',
        "cuid" => 'varchar',
        "uid" => 'int',
        "type" => 'int',
        "payclass" => 'int',
        "paymoney" => 'int',
        "paymoney_zk" => 'int',
        "express" => 'varchar',
        "express_code" => 'varchar',
        "express_money" => 'int',
        "pay_name" => 'varchar',
        "pay_phone" => 'varchar',
        "pay_zonecode" => 'varchar',
        "pay_cartid" => 'varchar',
        "pay_address" => 'varchar',
        "cont" => 'text',
        "addtime" => "int",
        "paytime" => "int",
        "status" => "int",
        "del_time" => "int",
    ];
    public function getArticle($data,$user = [],$che = true){
        $_aModel = "app\\common\\model\\{$data['model']}\\Article";
        $_aModel = new $_aModel;
        $_article = $_aModel->getOne($data['mid'],$data['aid']);
        $res = $stock = [];
        $_dir = date('Y-m');
        $_dir = "pay/{$_dir}";
        if(!empty($data['parametermoney'])){
            foreach ($data['parametermoney'] as $k => $v) {
                $_ve = Common::del_file($_article['parametermoney'], 'parameter',$v['parameter']);
                $_vp = $_ve['0'];
                $_v['oid'] = $che ? $data['oid'] : '';
                $_v['model'] = $data['model'];
                $_v['aid'] = $_article['id'];
                $_v['title'] = $_article['title'];
                if($che) {
                    $_v['picurl'] = empty($_article['picurl']) ? '' : \app\facade\wormview\Upload::fileMove($_article['picurl'], $_dir, 'copy', false);
                }else{
                    $_v['picurl'] = empty($_article['picurl']) ? '' : $_article['picurl'];
                }
                $_v['num'] = $v['num'];
                $_v['mid'] = $_article['mid'];
                $_v['money'] = $_vp['money'];
                $_v['money_zk'] = $_vp['money_zk'];
                $_money = empty($_v['money_zk']) ? $_v['money'] : $_v['money_zk'];
                $_sale_one = empty($_vp['sale_one']) ? '0' : $_vp['sale_one'];
                $_sale_two =  empty($_vp['sale_two']) ? '0' : $_vp['sale_two'];
                $_sale_three =  empty($_vp['sale_three']) ? '0' : $_vp['sale_three'];
                if(!empty($_article['_order_group']) && in_array($_article['_order_group'],['1','2','4'])){
                    if(in_array($_article['_order_group'],['1','2']) && !empty($user)){
                        $_money = empty($_vp["groupid_{$user['u_groupid']}"]) ? $_money : $_vp["groupid_{$user['u_groupid']}"];
                        $_v['money'] = $_money;
                        $_v['money_zk'] = $_vp['money_zk'] = $_sale_one = $_sale_two = $_sale_three = '0';
                    }else if($_article['_order_group'] == '4' && !empty($data['chinacode'])){
                        $_money = empty($_vp["chinacode_{$data['chinacode']}"]) ? $_money : $_vp["chinacode_{$data['chinacode']}"];
                        $_v['money'] = $_money;
                        $_v['money_zk'] = empty($_vp["chinacode_{$data['chinacode']}money_zk"]) ? $_money : $_vp["chinacode_{$data['chinacode']}money_zk"];
                        $_sale_one = $_vp["chinacode_{$data['chinacode']}sale_one"];
                        $_sale_two = $_vp["chinacode_{$data['chinacode']}sale_two"];
                        $_sale_three = $_vp["chinacode_{$data['chinacode']}sale_three"];
                    }
                }
                $_v['money_zon'] = empty($_v['money_zk']) ? $_money * $_v['num'] : $_v['money_zk'] * $_v['num'];
                $_v['fu_cont'] = empty($data['fu_cont']) ? '' : json_encode($data['fu_cont'], JSON_UNESCAPED_UNICODE);
                if($_article['stock_type'] == '1'){
                    $_vstock = [
                        'aid' => $_article['aid'],
                        'mid' => $_article['mid'],
                        'parameter' => empty($_vp['stock']) ? '0' : $v['parameter'],
                        'groupid' => empty($_vp['stock']) ? '0' : $_vp['groupid'],
                        'chinacode' => empty($_vp['stock']) ? '0' : $_vp['chinacode'],
                        'money_type' => $_vp['money_type'],
                        'num' => $_v['num'],
                    ];
                    $stock[] = $_vstock;
                }
                $_vp = [
                    "parameter" => $_vp['parameter'],
                    "money" => $_v['money'],
                    "money_zk" => $_v['money_zk'],
                    "num" => $v['num'],
                    "sale_one" => $_sale_one,
                    "sale_two" => $_sale_two,
                    "sale_three" => $_sale_three,
                ];
                $_v['admincont'] = json_encode($_vp, JSON_UNESCAPED_UNICODE);
                $_v['money'] = intval($_v['money'] * 100);
                $_v['money_zk'] = intval($_v['money_zk'] * 100);
                $_v['money_zon'] = intval($_v['money_zon'] * 100);
                $res[] = $_v;
            }
        }else{
            $_v['oid'] = $che ? $data['oid'] : '';
            $_v['model'] = $data['model'];
            $_v['aid'] = $_article['id'];
            $_v['title'] = $_article['title'];
            if($che) {
                $_v['picurl'] = empty($_article['picurl']) ? '' : \app\facade\wormview\Upload::fileMove($_article['picurl'], $_dir, 'copy', false);
            }else{
                $_v['picurl'] = empty($_article['picurl']) ? '' : $_article['picurl'];
            }
            $_v['num'] = $data['num'];
            $_v['mid'] = $_article['mid'];
            $_v['money'] = $_article['money'];
            $_v['money_zk'] = $_article['money_zk'];
            $_money = empty($_v['money_zk']) ? $_v['money'] : $_v['money_zk'];
            $_sale_one = empty($_article['sale_one']) ? '0' : $_article['sale_one'];
            $_sale_two = empty($_article['sale_two']) ? '0' : $_article['sale_two'];
            $_sale_three = empty($_article['sale_three']) ? '0' : $_article['sale_three'];
            if(!empty($_article['_order_group']) && in_array($_article['_order_group'],['1','2','4'])) {
                if (in_array($_article['_order_group'],['1','2','4']) && !empty($user)) {
                    $_money = empty($_article["groupid_{$user['u_groupid']}"]) ? $_money : $_article["groupid_{$user['u_groupid']}"];
                    $_v['money'] = $_money;
                    $_v['money_zk'] = $_vp['money_zk'] = $_sale_one = $_sale_two = $_sale_three = '0';
                } else if ($_article['_order_group'] == '4' && !empty($data['chinacode'])) {
                    $_money = empty($_article["chinacode_{$data['chinacode']}"]) ? $_money : $_article["chinacode_{$data['chinacode']}"];
                    $_v['money'] = $_money;
                    $_v['money_zk'] = empty($_article["chinacode_{$data['chinacode']}money_zk"]) ? $_money : $_article["chinacode_{$data['chinacode']}money_zk"];
                    $_sale_one = $_article["chinacode_{$data['chinacode']}sale_one"];
                    $_sale_two = $_article["chinacode_{$data['chinacode']}sale_two"];
                    $_sale_three = $_article["chinacode_{$data['chinacode']}sale_three"];
                }
            }
            $_v['money_zon'] = empty($_v['money_zk']) ? $_money * $_v['num'] : $_v['money_zk'] * $_v['num'];
            $_v['fu_cont'] = empty($data['fu_cont']) ? '' : json_encode($data['fu_cont'], JSON_UNESCAPED_UNICODE);
            if($_article['stock_type'] == '1'){
                $_vstock = [
                    'aid' => $_article['aid'],
                    'mid' => $_article['mid'],
                    'parameter' => '0',
                    'groupid' => '0',
                    'chinacode' => '0',
                    'money_type' => $_vp['money_type'],
                    'num' => $_v['num'],
                ];
                $stock[] = $_vstock;
            }
            $_vp = [
                "money" => $_v['money'],
                "money_zk" => $_v['money_zk'],
                "num" => $_v['num'],
                "sale_one" => $_sale_one,
                "sale_two" => $_sale_two,
                "sale_three" => $_sale_three,
            ];
            $_v['admincont'] = json_encode($_vp, JSON_UNESCAPED_UNICODE);
            $_v['money'] = intval($_v['money'] * 100);
            $_v['money_zk'] = intval($_v['money_zk'] * 100);
            $_v['money_zon'] = intval($_v['money_zon'] * 100);
            $res[] = $_v;
        }
        $shop = [
            'shoplist' => $che ? Upload::editadd($res) : $res,
            'stock' => $stock,
        ];
        return $shop;
    }
    //  保存订单
    public function setOne($data){
        $old = $this->whereOid($data['oid'])->find();
        if(empty($old)){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            if(!$this->whereOid($data['oid'])->update($data)){
                return false;
            }
        }
        $res = $this->whereOid($data['oid'])->find()->toArray();
        return $res;
    }
    public function getList($data = []){
        $map = $this;
        if(empty($data['del_time'])){
            $map = $map->whereDelTime('0');
        }else{
            $map = $map->where('del_time','>','0');
        }
        if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereIn('status',$data['status']);
        }
        $map = !empty($data['oid']) ? $map->whereIn('oid',is_array($data['oid']) ? $data['oid'] : (string) $data['oid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $map = !empty($data['payclass']) ? $map->wherePayclass($data['payclass']) : $map;
        $map = !empty($data['addtimes']) ? $map->where('addtime','>',$data['addtimes']['addtime'])->where('addtime','<',$data['addtimes']['endtime']) : $map;

        $getlist = $map->order('addtime desc')->withoutField('del_time')->paginate(empty($data['limit']) ? '24' : $data['limit']);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $getlist['data'] = $this->getPayDataList($getlist['data']);
        }
        $getlist['page'] = $page;
        return $getlist;
    }
    //  获取订单
    public function getOne($oid){
        $res = $this->getList(['oid' => $oid]);
        return $res['total'] > 0 ? $res['data']['0'] : [];
    }
    //  单个订单
    protected function getPayDataList($data)
    {
        $payData = new PayData();
        $oid = array_unique(array_column($data,'oid'));
        $oid = $payData->whereIn('oid',$oid)->select()->toArray();
        //  检测订单是否评论
        $_endbase = Common::del_file($data,'status','1');
        $_comment = [];
        if(!empty($_endbase)){
            $_oids = array_column($_endbase,'oid');
            $payData = new Comment();
            $_comment = $payData->whereIn('oid',$_oids)->select()->toArray();
        }
        //  获取用户
        $data = getUserList($data);
        foreach ($data as $k => $v){
            $v['shoplist'] = Common::del_file($oid,'oid',$v['oid']);
            $v['addtime'] = empty($v['addtime']) ? '' : date('Y-m-d H:i:s',(int) $v['addtime']);
            $v['playtime'] = empty($v['playtime']) ? '' : date('Y-m-d H:i:s',(int) $v['playtime']);
            $v['u_name'] = empty($v['userdb']['u_name']) ? '用户已删除' : $v['userdb']['u_name'];
            $v['u_uniname'] =  empty($v['userdb']['u_uniname']) ? '-' : $v['userdb']['u_uniname'];
            $v['u_uname'] =  empty($v['userdb']['u_uname']) ? '-' : $v['userdb']['u_uname'];
            $v['u_sex'] =  empty($v['userdb']['u_sex']) ? '-' : $v['userdb']['u_sex'];
            $v['u_phone'] =  empty($v['userdb']['u_phone']) ? '-' : $v['userdb']['u_phone'];
            $v['cont'] = empty($v['cont']) ? '' : json_decode($v['cont'],true);
            $v['paymoney'] = $v['paymoney'] / 100;
            $v['paymoney_zk'] = $v['paymoney_zk'] / 100;
            $v['express_money'] = $v['express_money'] / 100;
            foreach ($v['shoplist'] as $k1 => $v1){
                $v1['admincont'] = empty($v1['admincont']) ? '' : json_decode($v1['admincont'],true);
                $v1['fu_cont'] = empty($v1['fu_cont']) ? '' : json_decode($v1['fu_cont'],true);
                $v1['money'] = $v1['money'] / 100;
                $v1['money_zk'] = $v1['money_zk'] / 100;
                $v1['money_zon'] = $v1['money_zon'] / 100;
                $v['shoplist'][$k1] = $v1;
            }
            $v['comment_list'] = [];
            if($v['status'] == '1' && !empty($_comment)){
                $v['comment_list'] = Common::del_file($_comment,'oid',$v['oid']);
            }
            unset($v['userdb']);
            $data[$k] = $v;
        }
        $data = Upload::editadd($data,false);
        return $data;
    }

    public function CountList($data,$user,$oid = ''){
        $shoplist = [];
        foreach ($data['shoplist'] as $k => $v){
            $v['chinacode'] = empty($data['chinacode']) ? '' : $data['chinacode'];
            $v['oid'] = $oid;
            $v = $this->getArticle($v,$user);
            $shoplist[] = $v;
        }
        $_shoplist = array_column($shoplist,'shoplist');
        $_eshop = [];
        foreach ($_shoplist as $k => $v){
            $_eshop = array_merge($_eshop,$v);
        }
        $_shoplist = $_eshop;
        $_stock = array_column($shoplist,'stock');
        $_stock = Common::del_null($_stock);
        $_eshop = [];
        foreach ($_stock as $k => $v){
            $_eshop = array_merge($_eshop,$v);
        }
        $_stock = $_eshop;
        $shoplist = [
            'shoplist' => $_shoplist,
            'stock' => $_stock,
        ];
        return $shoplist;
    }
    //  删除订单
    public function DeleteOne($data){
        $old = $this->getList(['oid' => $data]);
        if($old['total'] == '0'){
            return false;
        }
        $old = Common::del_file($old['data'],'status','0');
        $old = array_column($old,'oid');
        $this->whereIn('oid',$old)->delete();
        $_md = new PayData();
        $_md->whereIn('oid',$old)->delete();
        $_md = new Empower();
        $_md->whereIn('oid',$old)->delete();
        $_md = new Server();
        $_md->whereIn('oid',$old)->delete();
        return true;
    }
}