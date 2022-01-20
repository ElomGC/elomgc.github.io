<?php
return [
    'default'         => env('database.driver', 'mysql'),
    'time_query_rule' => [],
    'auto_timestamp'  => true,
    'datetime_format' => 'Y-m-d H:i:s',
    'connections'     => [
        'mysql' => [
            'type'              => env('database.type', 'mysql'),
            'hostname'          => env('database.hostname', 'localhost'),
            'database'          => env('database.database', 'open_wormcms_com'),
            'username'          => env('database.username', 'open_wormcms_com'),
            'password'          => env('database.password', 'spstwRybxxRy5e3z'),
            'hostport'          => env('database.hostport', '3306'),
            'params'            => [],
            'charset'           => env('database.charset', 'utf8mb4'),
            'prefix'            => env('database.prefix', 'op_'),
            'deploy'            => 0,
            'rw_separate'       => false,
            'master_num'        => 1,
            'slave_no'          => '',
            'fields_strict'     => true,
            'break_reconnect'   => false,
            'trigger_sql'       => env('app_debug', true),
            'fields_cache'      => false,
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],
    ],
];