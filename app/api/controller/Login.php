<?php
declare(strict_types = 1);

namespace app\api\controller;
use app\common\controller\ApiBase;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\exception\ValidateException;
use app\common\model\User;

class Login extends ApiBase
{
    protected $middleware = [\app\home\middleware\Login::class];

    public function reg(User $UserModel){
        if(!$this->request->isPost()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['u_name']) && empty($data['u_phone'])){
            $this->result('','0','请输入用户名或手机号');
        }
        $data['u_name'] = empty($data['u_name']) ? $data['u_phone'] : $data['u_name'];
        try {
            validate('app\common\validate\User')->scene('reg')->check($data);
        }catch (ValidateException $e){
            $this->result('','0',$e->getError());
        }
        if($this->webdb['user_checkphone'] == '1' && empty($data['u_code']) || $data['u_code'] != cache($data['u_phone'].'_code')){
            $this->result('','0','验证码不正确');
        }
        $data['u_groupid'] = '2';
        $data['status'] = $this->webdb['user_status'] == '1' ? '1' : '0';
        $data = $UserModel->getEditAdd($data);
        if($res = $UserModel->setOne($data)){
            $user = getUser($res['uid'],'uid');
            $user = Upload::editadd($user,false);
            $utoken = $this->res_token($user);
            $user['token'] = $utoken['token'];
            $user['times'] = $utoken['times'];
            event('userlog', $user);
            $this->result($user,'1','注册成功');
        }
        $this->result('','0','绑定失败');
    }
    public function index(User $UserModel){
        if(!$this->request->isPost()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['u_name']) && empty($data['u_phone'])){
            $this->result('','0','请输入用户名或手机号');
        }
        if(empty($data['u_password']) && empty($data['u_code'])){
            $this->result('','0','请输入密码或验证码');
        }
        if(!empty($data['u_code'])){
            if(empty($data['u_code']) || $data['u_code'] != cache($data['u_phone'].'_code')){
                $this->result('','0','验证码不正确');
            }
            $res = getUser($data['u_phone'],'u_phone');
            $utoken = $this->res_token($res);
            $res['token'] = $utoken['token'];
            $res['times'] = $utoken['times'];
            $this->result($res,'1','登录成功');
        }else{
            $res = $UserModel->login($data);
            if($res['code'] == '1'){
                $utoken = $this->res_token($res['data']);
                $res['data']['token'] = $utoken['token'];
                $res['data']['times'] = $utoken['times'];
            }
            $this->result(empty($res['data']) ? [] : $res['data'],$res['code'],$res['msg']);
        }
    }
    public function repwd(User $UserModel){
        if(!$this->request->isPost()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        try {
            validate('app\common\validate\User')->scene('repwd')->check($data);
        }catch (ValidateException $e){
            $this->result('','0',$e->getError());
        }
        if($data['u_password'] != $data['ru_password']){
            $this->result('','0',"两次密码不一致");
        }
        if(empty($data['u_phone']) && $data['u_code'] != cache($data['u_phone'].'_code')){
            $this->result('','0',"验证码不正确");
        }
        $_user = getUser($data['u_phone'],'u_phone');
        $data['uid'] = $_user['uid'];
        $data = $UserModel->getEditAdd($data);
        if($res = $UserModel->setOne($data)){
            $this->result('','1','重置密码成功,请返回登录');
        }
        $this->result('','0','重置密码失败');
    }
}