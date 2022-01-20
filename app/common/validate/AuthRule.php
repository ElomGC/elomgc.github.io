<?php
declare(strict_types = 1);
namespace app\common\validate;

use think\Validate;
class AuthRule extends Validate {
   protected $rule =   [
        'title'  => 'require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u',
        'name'  => 'require|max:150',
        'status'  => 'require|number|in:0,1',
        'open'  => 'require|number|in:0,1',
        'menusee'  => 'require|number|in:0,1',
        'topsee'  => 'require|number|in:0,1',
        'type_class'  => 'require|number|in:0,1,2,3',
        'pid'  => 'number',
        'id'  => 'require|number',
        'sort'  => 'require|number',
        'condition'  => 'min:3',
    ];

    protected $message  =   [
        'title.require' => '权限名称不能为空！',
        'title.max' => '权限名称不得大于80字符！',
        'title.regex' => '权限名称不得使用特殊字符！',
        'name.require' => '规则地址不能为空！',
        'name.max' => '规则地址不得大于150字符！',
        'status.require' => '填写错误！',
        'status.number' => '填写错误！',
        'status.in' => '填写错误！',
        'open.require' => '填写错误！',
        'open.number' => '填写错误！',
        'open.in' => '填写错误！',
        'menusee.require' => '填写错误！',
        'menusee.number' => '填写错误！',
        'menusee.in' => '填写错误！',
        'topsee.require' => '填写错误！',
        'topsee.number' => '填写错误！',
        'topsee.in' => '填写错误！',
        'pid.number' => '上级分类选择错误！',
        'id.require' => '填写错误！',
        'id.number' => '填写错误！',
        'sort.require' => '请填写排序值！',
        'sort.number' => '排序值错误！',
        'type_class.require' => '请选择权限类型！',
        'type_class.number' => '权限类型选择错误',
        'type_class.in' => '权限类型选择错误',
    ];
    protected $scene = [
        'add' => ['title','name','status','open','menusee','type_class','pid','sort','topsee','condition'],
        'edit' => ['title','name','status','open','menusee','type_class','pid','sort','topsee','condition','id'],
        'fastedit' => ['id','name' => 'max:150','sort' => 'number']
    ];
}