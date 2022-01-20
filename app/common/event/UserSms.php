<?php
declare(strict_types = 1);

namespace app\common\event;

use app\common\model\UserSms as cxModel;
use GatewayClient\Gateway;

class UserSms
{
    /**
     * @param $data 要发送的数据
     * @param string|array $type 可选值sms,usersms,usermoney
     * @return bool
     */
    public function handle($data){
        if(is_array($data['_type'])){
            foreach ($data['_type'] as $k => $v){
                $sendname = "send{$v}";
                if(!method_exists($this,$sendname)){
                    continue;
                }
                $this->$sendname($data);
            }
            return true;
        }
        $sendname = "send{$data['_type']}";
        if(!method_exists($this,$sendname)){
            return true;
        }
        $this->$sendname($data);
        return true;
    }
    //  保存站内信消息
    protected function sendsms($data)
    {
        if(!is_file(app_path()."common\model\Sms.php")){
            return true;
        }
        $smsModel = "app\\common\\model\\Sms";
        $smsModel = new $smsModel;
        $data = $smsModel->getEditAdd($data);
        $smsModel->setOne($data);
        return true;
    }
    protected function sendusersms($data)
    {
        $smsModel = new cxModel();
        $data = $smsModel->getEditAdd($data);
        if($res = $smsModel->setOne($data)){
            Gateway::$registerAddress = '127.0.0.1:23462';
            if(Gateway::isOnline($res['to_uid'])){
                Gateway::sendToUid($res['to_uid'], json_encode($res,JSON_UNESCAPED_UNICODE));
            }
            if($res['fo_uid'] != '0'){
                if(Gateway::isOnline($res['fo_uid'])) {
                    Gateway::sendToUid($res['fo_uid'], json_encode($res, JSON_UNESCAPED_UNICODE));
                }
            }
            return true;
        }
        return false;
    }
}