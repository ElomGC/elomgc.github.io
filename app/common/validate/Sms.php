<?php
/**
 *  蠕虫CMS网站管理系统 wormcms.com
 *  Created by cxbs.net.
 *  User: 赵焱
 *  Email: 840712498@qq.com
 *  Date: 2019-03-09
 *  Time: 15:49
 */

namespace app\common\validate;

use think\Validate;

class Sms extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "type" => "require|number",
        "title" => "require|min:2|max:80",
        "phone" => "require|mobile",

    ];
    protected $message  =   [
        'id.require' => '非法访问！',
        'id.number' => '非法访问！',
        'type.require' => '请选择类型！',
        'type.number' => '请选择类型！',
        'title.require' => '请输入标题！',
        'title.min' => '标题不得小于2字符！',
        'title.max' => '标题不得大于80字符！',
        'phone.require' => '请输入手机号！',
        'phone.mobile' => '手机号码输入错误！',

    ];
    protected $scene = [
        'reg' => ['type','title','phone'],
    ];
}