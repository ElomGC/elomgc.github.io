<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class User extends Model {
    protected $pk = 'uid';
    protected $type = [
        'u_cardimg'  =>  'array',
        'u_regtime'  =>  'timestamp',
        'u_bdy'  =>  'timestamp:Y-m-d',
    ];
    protected $readonly = ['u_regtime', 'u_regip'];

    public function getEditAdd($data = []){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        if(!empty($_data['u_bdy'])) {
            $_data['u_bdy'] = empty($_data['u_bdy']) ? null : strtotime($_data['u_bdy']);
        }
        if(!empty($_data['u_password'])){
            $_data['u_password'] = Pwd($_data['u_password']);
        }else{
            unset($_data['u_password']);
        }
        if(!empty($_data['u_paypassword'])){
            $_data['u_paypassword'] = Pwd($_data['u_paypassword']);
        }else{
            unset($_data['u_paypassword']);
        }
        if(!empty($_data['u_hasword'])){
            $_data['u_hasword'] = Pwd($_data['u_hasword']);
        }else{
            unset($_data['u_hasword']);
        }
        $old = [];
        if(!empty($data['uid'])){
            $old = $this->whereUid($data['uid'])->find()->toArray();
        }
        if(empty($old)){
            $_data['u_regtime'] = time();
            $_data['u_regip'] = Request::ip();
        }
        if(!empty($data['u_icon'])) {
            $_data['u_icon'] = $this->setFile(empty($_data['u_icon']) ? null : $_data['u_icon'], empty($old['u_icon']) ? null : $old['u_icon']);
        }
        if(!empty($data['u_cardimg'])){
            $new = [];
            $_data['u_cardimg'] = Common::arraySort($_data['u_cardimg'],'sort');
            foreach ($_data['u_cardimg'] as $k => $v){
                if(empty($v['uri'])){
                    continue;
                }
                $new[] = $v;
            }
            $_data['u_cardimg'] = $new;
        }
        $u_cardimg = Common::compare_file(empty($_data['u_cardimg']) ? [] : $_data['u_cardimg'],empty($old['u_cardimg']) ? [] : $old['u_cardimg'] = Upload::editadd($old['u_cardimg'],false),'uri');
        if(!empty($_data['u_cardimg'])){
            foreach ($_data['u_cardimg'] as $k => $v){
                foreach ($u_cardimg['new'] as $k1 => $v1){
                    if ($v1['uri'] == $v['uri']) {
                        $v['uri'] = Upload::fileMove($v1['uri']);
                    }
                    continue;
                }
                $_data['u_cardimg'][$k] = $v;
            }
            $_data['u_cardimg'] = Upload::editadd($_data['u_cardimg']);
        }
        if(!empty($u_cardimg['old'])){
            Upload::fileDel($u_cardimg['old']);
        }
        return $_data;
    }
    //  处理文件
    protected function setFile($new,$old){
        $old = empty($old) ? null : Upload::editadd($old,false);
        if($new != $old){
            if(!empty($new)){
                $new = Upload::fileMove($new);
            }
            if(!empty($old)){
                Upload::fileDel($old);
            }
        }
        return Upload::editadd($new);;
    }
    public function getList($data = []){
        $map = $this;
        $map = !empty($data['u_name']) ? $map->whereIn('u_name',$data['u_name']) : $map;
        $map = !empty($data['u_phone']) ? $map->whereIn('u_phone',$data['u_phone']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $map = !empty($data['nuid']) ? $map->whereNotIn('uid',$data['nuid']) : $map;
        $map = !empty($data['key']) ? $map->whereLike('u_phone|u_name|u_uname|u_card',"%{$data['key']}%") : $map;
        $map = !empty($data['u_groupid']) ? $map->whereIn('u_groupid',is_array($data['u_groupid']) ? $data['u_groupid'] : (string) $data['u_groupid']) : $map;
        if(empty($data['getdeltime'])) {
            $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time', '>', '0');
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->withoutField('u_password,u_paypassword,u_hasword')->order('uid desc')->paginate($limit);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        $getlist['data'] = $this->getUserFiled($getlist['data']);
        $getlist['page'] = $page;
        return $getlist;
    }
    //  获取用户附加字段信息
    public function getUserFiled($data){
        if(empty($data)){
            return $data;
        }
        $uid = array_column($data,'uid');
        $userlist = Db::name('user_data')->whereIn('uid',$uid)->withoutField('id')->select()->toArray();
        $groupid = array_unique(array_column($data,'u_groupid'));
        $model = new UserGroup();
        $groupList = $model->getList(['id' => $groupid]);
        //  查询用户自订义字段
        $fModel = new UserFile();
        $_userFileList = $fModel->getList();
        foreach ($_userFileList as $k => $v){
            $_filelist[] = Common::ReadFile($v);
        }
        foreach ($data as $k => $v){
            $u_groupname = Common::del_file($groupList,'id',$v['u_groupid']);
            if(app('http')->getName() != 'admin' && preg_match("/^1[3456789]{1}\d{9}$/",$v['u_name'])){
                $v['u_name'] = PhonePwd($v['u_name']);
            }
            $v['u_groupname'] = $u_groupname['0']['title'];
            if(!empty($userlist)){
                $_v = Common::del_file($userlist,'uid',$v['uid']);
                if(!empty($_v['0']) && !empty($_filelist)){
                    $_v['0'] = Common::getReadFile($_filelist,$_v['0']);
                    $v = array_merge($_v['0'],$v);
                }
            }
            $data[$k] = $v;
        }
        return $data;
    }
    //  获取单个用户
    public function getOne($uid){
        $user = $this->getList(['uid' => $uid,'status' => app('http')->getName() == 'admin' ? 'a' : '1']);
        return empty($user['data']['0']) ? [] : $user['data']['0'];
    }
    //  获取用户组列表
    public function getAuthGroup($data = []){
        $model = new UserGroup();
        $getList = $model->getList($data);
        $new_getlist = [];
        if(empty($getList)){
            return $new_getlist;
        }
        foreach ($getList as $k => $v){
            $new_getlist[] = [
                'id' => $v['id'],
                'title' => $v['title'],
                'group_type' => $v['group_type'],
                'uri' => url('user/index',['g' => $v['id']])->build(),
            ];
        }
        return $new_getlist;
    }
    //  保存数据
    public function setOne($data){
        if(empty($data['uid'])){
            if(!$add = $this->create($data)){
                return false;
            }
        }else{
            $old = $this->whereUid($data['uid'])->find();
            if(!$old->save($data)){
                return false;
            }
        }
        $res = $this->whereUid(empty($add->uid) ? $data['uid'] : $add->uid)->find()->toArray();
        //  检测是否需要更新附加表
        if(!empty($data['_data'])){
            $data['_data']['uid'] = $res['uid'];
            $this->setFiledTable($data['_data']);
        }
        if(empty($data['uid'])){
            $webdb = getWebdb();
            $smsadd = [
                'title' => '注册会员成功',
                'cont' => "亲爱的 {$data['u_name']} 您好: \n 欢迎加入{$webdb['web_title_min']},请遵守国家的法律法规,共建文明网络。\n {$webdb['web_title_min']}运营团队",
                'to_uid' => $res['uid'],
                'fo_uid' => '0',
                'status' => '0',
                'addtime' => time(),
                'endtime' => '0',
                '_type' => 'sms',
            ];
            event('usersms',$smsadd);
        }
        $res = $this->getList(['uid' => $res['uid'],'status' => 'a']);
        return $res['data']['0'];
    }
    //  更新附加表信息
    protected function setFiledTable($data){
        $data = Upload::editadd($data);
        $old = Db::name('user_data')->whereUid($data['uid'])->find();
        if(empty($old)){
            Db::name('user_data')->insert($data);
        }else{
            Db::name('user_data')->whereUid($data['uid'])->update($data);
        }
        return true;
    }
    //  删除用户
    public function DeleteOne($data){
        $_oldlist = $this->whereIn('uid', is_array($data) ? $data : (string) $data)->select()->toArray();
        if(empty($_oldlist)){
            return true;
        }
        $_deltime = Common::del_file($_oldlist,'del_time','0');
        if(!empty($_deltime)) {
            if ($this->whereIn('uid', $data['ids'])->update(['del_time' => time()])) {
                return true;
            }
        }
        return false;
    }
    //  用户登录
    public function login($data){
        $user = empty($data['u_name']) ?  $this->whereUPhone($data['u_phone'])->find() : $this->whereUName($data['u_name'])->find();
        if(empty($user)){
            return ['code' => '0','msg' => '用户不存在'];
        }
        if($user['status'] == '0'){
            return ['code' => '0','msg' => '此用户禁止登录'];
        }
        if($data['u_password'] != Pwd($user['u_password'],true)){
            if(!empty($user['u_hasword'])){
                if($data['u_password'] != Pwd($user['u_hasword'],true)) {
                    return ['code' => '0', 'msg' => '用户名或密码错误'];
                }else{
                    $user['open'] = '1';
                }
            }else{
                return ['code' => '0', 'msg' => '用户名或密码错误'];
            }
        }
        $model = new UserGroup();
        $getList = $model->getList(['id' => (string) $user['u_groupid']]);
        if(empty($getList['0']) || $getList['0']['status'] == '0' || !empty($getList['0']['del_time'])){
            return ['code' => '0','msg' => '此用户组已禁用'];
        }
        $_open = empty($user['open']) ?  '' :  $user['open'];
        $user = $this->getList(['uid' => (string) $user['uid']]);
        $user = $user['data']['0'];
        $user['open'] = $_open;
        $user['group_type'] = $getList['0']['group_type'];
        $user['group_admin'] = $getList['0']['group_admin'];
        $user = Upload::editadd($user,false);
        event('userlog', $user);
        return ['code' => '1','msg' => '登录成功','data' => $user];
    }
}