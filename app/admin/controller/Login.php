<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\Base;
use app\common\wormview\AddEditList;
use app\common\validate\User as userValidate;
use app\common\model\User as userModel;
use app\facade\hook\Common;
use think\exception\ValidateException;

class Login extends Base
{
    use AddEditList;
    protected $form_list;
    protected $list_temp = 'login';
    protected $list_base = false;
    protected function initialize(){
        parent::initialize();
        $this->tempview();
    }

    public function login(userModel $userModel){
        if(session('?_admin_')){
            $this->redirect(url('index/index')->build());
        }
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            if($this->webdb['web_adminyz'] == '1' && !captcha_check($data['u_captcha'])){
                $this->error("验证码不正确");
            }
            try {
                validate(userValidate::class)->scene('login')->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $add = $userModel->login($data);
            if($add['code'] == '1'){
                $this->success("登录成功",url('index/index')->build());
            }
            event('userlog', $data);
            $this->error("用户名或密码不正确");
        }
        return $this->viewAdminAdd();
    }
    //  退出
    public function qulogin(){
        session('userdb',null);
        session('_admin_',null);
        $this->success("退出成功",url('login/login')->build());
    }
}