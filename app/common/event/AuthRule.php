<?php
declare(strict_types = 1);

namespace app\common\event;

use app\common\model\AuthRule as cxModel;
use app\common\model\AuthRuleclass;
use app\common\model\UserGroup;
use app\facade\hook\Common;
use worm\NodeFormat;

class AuthRule
{
    //  添加操作事件
    public function handle($data){
        //  检测用户是否为开发者
        if($data['open'] == '1'){
            return $this->AuthList([],true);
        }
        $userAuth = empty($data['userauth']) ? '' : $data['userauth'];
        $userGroup = $this->GroupAuth($data['u_groupid']);
        if(!$userGroup){
            return true;
        }
        $userAuth = empty($userAuth) ? $userGroup['authList'] : explode(',',$data['userauth']);
        if(!empty($userAuth)){
            return $this->AuthList($userAuth);
        }
        if($userGroup['group_type'] == '1'){
            return true;
        }
        return $this->AuthList();
    }
    //  查询用户组权限
    protected function GroupAuth($gid){
        $groupModel = new UserGroup();
        $group = $groupModel->getList(['id' => $gid]);
        if(count($group) < 1 || $group['0']['status'] != '1' || $group['0']['del_time'] > '0'){
            return false;
        }
        $group = $group['0'];
        $group['authList'] = empty($group['rules']) ? [] :$group['rules'];
        $group['authList'] = empty($group['mrules']) ? $group['authList'] : array_merge($group['authList'],$group['mrules']);
        $group['authList'] = empty($group['arules']) ? $group['authList'] : array_merge($group['authList'],$group['arules']);
        return $group;
    }
    //  保存用户权限
    protected function AuthList($data = [],$open = false){
        $_user = session('userdb');
        $open = $_user['u_groupid'] == '1' && $_user['uid'] == '1' ? true : $open;
        $cxmodel = new cxModel();
        $authClassModel = new AuthRuleclass();
        $authlist = $cxmodel->getList(['open' => '1']);
        $_authlist = !$open ? Common::del_file(Common::del_file($authlist,'id',$data),'open','0') : $authlist;
        $Noauthlist = Common::del_file($authlist,'id',array_column($_authlist,'id'),true);
        //  获取权限分类
        $authclss = $authClassModel->getList();
        $_authclss = $Noauthclss = [];
        foreach ($authclss as $k => $v){
            $id = $v['id'];
            $_authclss[$id]['c'] = Common::del_file($_authlist,'type_class',$id);
            $_authclss[$id]['c'] = NodeFormat::toList($_authclss[$id]['c']);
            $_authclss[$id]['n'] = Common::del_file($Noauthlist,'type_class',$id);
        }
        cache('userAuth_'.$_user['uid'],$_authclss);
    }
}