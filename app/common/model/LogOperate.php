<?php
declare(strict_types = 1);

namespace app\common\model;

use think\facade\Request;
use think\Model;

class LogOperate extends Model {
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $insert = 'addtime';
    protected $type = [
        'addtime'    => 'timestamp',
    ];
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $updateTime = false;
    protected $schema = [
        'id'          => 'int',
        'uid'         => 'int',
        'cont'        => 'string',
        'type'        => 'tinyint',
        'addip'       => 'string',
        'addtime'     => 'int',
    ];


}