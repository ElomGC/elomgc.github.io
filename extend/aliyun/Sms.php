<?php
declare (strict_types = 1);
namespace aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms {
    // 初始化
    protected function aliyun($data,$config,$TemplateCode){
        AlibabaCloud::accessKeyClient($config['accessKeyId'], $config['accessSecret'])->regionId('cn-hangzhou')->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action($TemplateCode)
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => $data,
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            $res = array(
                'Message' =>  $e->getErrorMessage() . PHP_EOL,
            );
            return $res;
        } catch (ServerException $e) {
            $res = array(
                'Message' =>  $e->getErrorMessage() . PHP_EOL,
            );
            return $res;
        }
    }
    //  发送短信
    public function SendSms($data){
        $config = ['accessKeyId' => $data['sms_key'],'accessSecret' => $data['sms_secret']];
        $sms = ['RegionId' => 'cn-hangzhou','PhoneNumbers' => $data['phone'],'SignName' => $data['sing'],'TemplateCode' => $data['sms_reg'],'TemplateParam' => $data['TemplateParam']];
        return $this->aliyun($sms,$config,'SendSms');
    }
}