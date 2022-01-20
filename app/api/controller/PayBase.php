<?php
declare(strict_types = 1);

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\model\PayLinshi;
use app\common\model\PayBase as cxModel;
use app\common\model\Sms;
use app\common\model\UserMoney;
use weixin\WxBase;

class PayBase extends ApiBase {
    protected $types;
    public function wxpayend(WxBase $wxBase){
        $this->types = $this->is_weixin();
        if(!empty($this->getdata['logintype']) && $this->getdata['logintype'] == 'wx-mp'){
            $this->types = 'wx-mp';
        }
        $endplay = $wxBase->get_payend($this->webdb,$this->getdata['out_trade_no'],$this->types == 'wx-mp' ? 'wx-mp' : 'wx');
        if($endplay['return_code'] == 'SUCCESS' && $endplay['result_code'] == 'SUCCESS' && $endplay['trade_state'] == 'SUCCESS'){
            $order['pricemoney'] = $endplay['total_fee'];
            $order['status'] = $endplay['transaction_id'];
            $order['lsoid'] = $endplay['out_trade_no'];
            $order['pupusermoney'] = empty($getdata['pupusermoney']) ? null : $getdata['pupusermoney'];
            if($this->PayEnd($order)){
                $this->result([],'1','支付成功');
            }
        }
        $this->result([],'0','未支付');
    }
    public function PayEnd($data):bool
    {
        $PlModel = new PayLinshi();
        $PModel = new cxModel();
        //  获取临时订单信息
        $_Lone = $PlModel->whereLsoid($data['lsoid'])->find();
        $PlModel->whereOid($_Lone['oid'])->whereNotIn('lsoid',(string) $data['lsoid'])->delete();
        $PlModel->whereLsoid($data['lsoid'])->update(['type' => $this->types,'pay_no' => $data['status']]);
        $_Old = $PModel->getOne($_Lone['oid']);
        if($_Old['paytime'] > '0'){
            return true;
        }
        $paytime = time();
        $PModel->whereOid($_Lone['oid'])->update(['status' => '1','paytime' => $paytime]);
        //  获取订单详情
        $_Olist = $_Old['shoplist'];
        $_userlist = $_smslist = [];
        $_vtitle = array_column($_Olist,'title');
        $_vtitle = implode(',',$_vtitle);
        $money = empty($_Old['paymoney_zk']) ? $_Old['paymoney'] : $_Old['paymoney_zk'];
        $_money = $money * 100;
        $_userlist[] = [
            'oid' => $_Lone['oid'],
            'uid' => $_Old['uid'],
            'puid' => '0',
            'add_type' => '101',
            'money_type' => '0',
            'money' => $_money,
            'cont' => "用户充值，收入 {$money} 元。",
            'addtime' => time(),
            'addip' => $this->request->ip(),
        ];
        $_userlist[] = [
            'oid' => $_Lone['oid'],
            'uid' => $_Old['uid'],
            'puid' => '0',
            'add_type' => '2001',
            'money_type' => '0',
            'money' => $_money,
            'cont' => "订购了 {$_vtitle}，支出 {$money} 元。",
            'addtime' => time(),
            'addip' => $this->request->ip(),
        ];
        $_smslist[] = [
            'to_uid' => $_Old['uid'],
            'fo_uid' => '0',
            'title' => "订单支付成功",
            'cont' => "亲爱的 {$_Old['pay_name']} 您好: \n \n 您已成功订购了 {$_vtitle}，我们将尽快为您服务。\n \n {$this->webdb['web_title_min']}运营团队",
            'status' => '0',
            'addtime' => time(),
            'endtime' => '0',
        ];
        $UmModel = new UserMoney();
        foreach ($_userlist as $k => $v){
            $_userlist[$k] = $UmModel->new_add($v);
        }
        $UmModel = new Sms();
        $UmModel->saveAll($_smslist);
        return true;
    }

}