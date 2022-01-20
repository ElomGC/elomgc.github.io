<?php
declare(strict_types = 1);
namespace app\common\validate;

use think\Validate;
class AuthRuleclass extends Validate {
   protected $rule =   [
        'title'  => 'require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u',
        'status'  => 'require|number|in:0,1',
        'id'  => 'require|number',
        'sort'  => 'require|number',
    ];

    protected $message  =   [
        'title.require' => '名称不能为空！',
        'title.max' => '名称不得大于80字符！',
        'title.regex' => '名称不得使用特殊字符！',

        'status.require' => '填写错误！',
        'status.number' => '填写错误！',
        'status.in' => '填写错误！',

        'id.require' => '填写错误！',
        'id.number' => '填写错误！',

        'sort.require' => '请填写排序值！',
        'sort.number' => '排序值错误！',

    ];
    protected $scene = [
        'add' => ['title','status','sort'],
        'edit' => ['title','status','sort','id'],
        'fastedit' => ['id','sort' => 'number']
    ];
}