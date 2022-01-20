<?php

return [
    'bind' => [
        'logadd' => 'LogAdd',
        'userlog' => 'UserLogin',
        'authrule' => 'AuthRule',
        'usersms' => 'UserSms',
    ],
    'listen' => [
        "LogAdd" => ['app\common\event\LogAdd'],
        "UserLogin" => ['app\common\event\UserLogin'],
        "AuthRule" => ['app\common\event\AuthRule'],
        "UserSms" => ['app\common\event\UserSms'],
    ],
];