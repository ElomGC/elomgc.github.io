<?php
declare(strict_types = 1);
namespace app\common\event;

use app\common\model\LogOperate;
use think\facade\Request;

class LogAdd {

    //  添加操作事件
    public function handle(LogOperate $cxmodel,$data){
        $data['uid'] = empty($data['uid']) ? session('usedb.uid') : $data['uid'];
        //  添加记录
        $add = [
            'uid' => empty($data['uid']) ? '0' : $data['uid'],
            'cont' => $data['log_title'],
            'type' => app('http')->getName(),
            'addtime' => time(),
            'addip' => Request::ip(),
        ];
        $cxmodel->create($add);
        return true;
    }
}