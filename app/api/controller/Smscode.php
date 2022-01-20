<?php
declare(strict_types = 1);

namespace app\api\controller;


use app\common\controller\ApiBase;
use app\facade\hook\Common;
use think\exception\ValidateException;
use aliyun\Sms as AliyunSms;
use app\common\model\SmsCode as cxModel;
use think\facade\Request;

class Smscode extends ApiBase {

    public function phonecode(AliyunSms $aliyun,cxModel $cxModel){
        if(!$this->request->isPost()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        try {
            validate('app\common\validate\Sms')->scene("reg")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        //  查询用户信息
        $user = getUser($data['phone'],'u_phone');
        switch ($data['type']){
            case '1001':
                if(!empty($user)){
                    $this->error("手机已存在");
                }
                break;
            case '1002':
                if(!empty($user['code'])){
                    $this->error("用户不存在");
                }
                break;
        }
        $code = ['code' => get_range(6)];
        cache($data['phone'].'_code',$code['code'],'600');
        $data['sms_key'] = $this->webdb['sms_key'];
        $data['sms_secret'] = $this->webdb['sms_secret'];
        $data['sing'] = $this->webdb['sms_sing'];
        $data['sms_reg'] = $this->webdb['sms_reg'];
        $data['TemplateParam'] = json_encode($code, JSON_UNESCAPED_UNICODE);
        $getlist = $aliyun->SendSms($data);
        $add = [
            'phone' => $data['phone'],
            'title' => $data['title'],
            'cont' => $code['code'],
            'addtime' => time(),
            'addip' => Request::ip(),
        ];
        if($getlist['Message'] == 'OK' && $getlist['Code'] == 'OK'){
            $add['status'] = '1';
        }else{
            $add['status'] = '0';
            $add['rescont'] = $getlist['Message'];
        }
        $add = $cxModel->getEditAdd($add);
        $cxModel->setOne($add);
        if($add['status'] == '1'){
            $this->success('短信发送成功');
        }
        $this->error('短信发送失败');
    }

}