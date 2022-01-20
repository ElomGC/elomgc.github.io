<?php
declare(strict_types = 1);

namespace app\home\controller;

use app\common\controller\HomeBase;
use app\common\model\User;
use app\common\model\UserApi;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\Cache;

class Login extends HomeBase
{
    use AddEditList;
    protected $middleware = [\app\home\middleware\Login::class];
    protected $resurl;
    protected function initialize(){
        parent::initialize();
        $this->resurl = url("index/index")->build();
        if(cache('login_url')){
            $this->resurl = cache('login_url');
        }
        if($this->request->action() != 'qulogin' && !empty($this->wormuser)){
            $this->error("您已登录",$this->resurl);
        }
    }
    public function login(User $cxModel){
        if($this->request->isPost()) {
            $data = Common::data_trim(input('post.'));
            try {
                validate('app\common\validate\User')->scene("login")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $res = $cxModel->login($data);
            if($res['code'] == '1'){
                $this->success('登录成功',$this->resurl);
            }
            $this->error($res['msg']);
        }
        $this->list_temp = $this->tempview."login.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewWeb($this->list_temp);
    }
    public function reg(User $userModel){
//        if($this->webdb['user_open'] == '0'){
//            $this->error("注册暂未开放");
//        }
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            $data['u_name'] = empty($data['u_name']) ? $data['u_phone'] : $data['u_name'];
            try {
                validate('app\common\validate\User')->scene("reg")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            if(empty($data['ru_password']) || $data['ru_password'] != $data['u_password']){
                $this->error("两次密码不一致");
            }
            if(!empty($this->webdb['user_checkphone']) && $this->webdb['user_checkphone'] == '1' && (empty($data['u_code']) || $data['u_code'] != cache($data['u_phone'].'_code'))){
                $this->error("验证码不正确");
            }
            $data['u_groupid'] = '3';
            $data['status'] = !empty($this->webdb['user_status']) && $this->webdb['user_status'] == '1' ? '1' : '0';
            $data = $userModel->getEditAdd($data);
            if($res = $userModel->setOne($data)){
                $wxMoel = new UserApi();
                $wx_data = $wxMoel->whereUid($res['uid'])->whereSubscribe('1')->count();
                if($wx_data < 1){
                    Cache::set('wxbinds',false);
                }
                $this->success($res['status'] == '1' ? "注册成功,请登录" : '注册成功，等待管理员审核',url('login')->build());
            }
            $this->error("注册失败");
        }
        $this->list_temp = $this->tempview."reg.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewWeb($this->list_temp);
    }
    public function repwd(User $userModel){
        if($this->request->isPost()) {
            $data = Common::data_trim(input('post.'));
            if (empty($data['u_phone'])) {
                $this->error("请输入手机号");
            }
            if (empty($data['u_code']) || $data['u_code'] != cache($data['u_phone'].'_code')) {
                $this->error("验证码不正确");
            }
            if ($data['ru_password'] != $data['u_password']) {
                $this->error("两次密码不一致");
            }
            try {
                validate('app\common\validate\User')->scene("repwd")->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $old = getUser($data['u_phone'],'u_phone');
            if($old['del_time'] > '0' || $old['status'] != '1'){
                $this->error("此用户已禁用，请联系管理员");
            }
            $add = [
                'uid' => $old['uid'],
                'u_password' => Pwd($data['u_password']),
            ];
            if($userModel->setOne($add)){
                $this->success("密码重置成功",url('login')->build());
            }
            $this->error("密码重置失败");
        }
        $this->list_temp = $this->tempview."repwd.htm";
        if(!is_file($this->list_temp)){
            $this->error("{$this->list_temp}模板不存在",url('Index/index')->build());
        }
        return $this->viewWeb($this->list_temp);
    }
    //  退出
    public function qulogin(){
        session('_admin_',null);
        session('userdb',null);
        Cache::set("wxbinds{$this->wormuser['uid']}",null);
        $this->success("退出成功",url('Index/index')->build());
    }
}