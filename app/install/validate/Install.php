<?php
/**
 * Created by PhpStorm.
 * User: 84071
 * Date: 2017-09-07
 * Time: 15:44
 */
namespace app\install\validate;
use think\Validate;
class Install extends Validate{
    protected $rule =   [
        'hostname|数据库地址'  => 'require|regex:/^[a-zA-Z0-9_.@~!?]{5,20}$/',
        'database|数据库名'  => 'require|regex:/^[a-zA-Z0-9_.@~!?]{4,50}$/',
        'username|数据库用户名'  => 'require|regex:/^[a-zA-Z0-9_-]{4,30}$/',
        'password|数据库密码'  => 'require|regex:/^[a-zA-Z0-9_.@~!?]{4,30}$/',
        'hostport|数据库端口'  => 'require|number',
        'prefix|数据表前缀'  => 'require|regex:^[a-z0-9]{1,20}[_]{1}',

        'web_title'  => 'require',
        'web_url'  => 'require|url',
        'u_name'  => 'require',
        'u_password'  => 'require|regex:/^[a-zA-Z0-9_.@~!?]{6,30}$/',
    ];

    protected $scene = [
        'database' => ['hostname','database','username','password','hostport','prefix'],
        'webdata' => ['web_title','web_url','u_name','u_password'],

    ];
}