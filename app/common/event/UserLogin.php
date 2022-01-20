<?php
declare(strict_types = 1);
namespace app\common\event;
use app\common\model\LogUserlog;
use think\facade\Request;

class UserLogin {

    //  添加操作事件
    public function handle(LogUserlog $cxmodel,$data){
        //  添加记录
        $add = [
            'uid' => empty($data['uid']) ? '0' : $data['uid'],
            'cont' => empty($data['uid']) ? "用户名或密码不正确，用户名【{$data['u_name']}】，密码【{$data['u_password']}】" : "用户登录成功，密码已加密",
            'type' => app('http')->getName(),
            'addtime' => time(),
            'addip' => Request::ip(),
        ];
        if(!empty($data['uid'])){
            $session_name = $add['type'] == 'admin' ? '_admin_' : 'userdb';
            $data['open'] = !empty($data['open']) && $data['open'] == '1' ? '1' : '0';
            session('userdb',$data);
            if($session_name == '_admin_'){
                session('_admin_',$data);
            }
        }
        $cxmodel->create($add);
        return true;
    }

}