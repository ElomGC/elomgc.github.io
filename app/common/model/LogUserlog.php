<?php
declare(strict_types = 1);

namespace app\common\model;

use think\Model;

class LogUserlog extends Model {
    // 开启自动写入时间戳
    protected $type = [
        'addtime'    => 'timestamp',
    ];
    // 定义时间戳字段名
    protected $schema = [
        'id'          => 'int',
        'uid'         => 'int',
        'cont'        => 'string',
        'type'        => 'tinyint',
        'addip'       => 'string',
        'addtime'     => 'int',
    ];


}