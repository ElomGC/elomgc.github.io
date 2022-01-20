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

class UserGroup extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "pid" => "number",
        "title" => "require|min:2|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u",
        "status" => "require|number|in:0,1",
        "rules" => "require|number",
        "group_up" => "require|number",
        "group_money" => "number",
        "group_type" => "require|number|in:0,1",
        "group_admin" => "require|number|in:0,1",
        "group_space" => "require|number",
        "sort" => "require|number",
        '__token__' => 'token',
    ];
    protected $message  =   [
        'id.require' => '非法访问！',
        'id.number' => '非法访问！',
        'pid.require' => '上级用户选择错误！',
        'pid.number' => '上级用户选择错误！',
        'title.require' => '用户组名称不能为空！',
        'title.min' => '用户组名称不得小于2字符！',
        'title.max' => '用户组名称不得大于80字符！',
        'title.regex' => '用户组名称不得使用特殊字符！',
        'status.require' => '请选择是否启用！',
        'status.number' => '请选择是否启用！',
        'status.in' => '请选择是否启用！',
        'group_money.require' => '请选择升级费用类型！',
        'group_money.number' => '请选择升级费用类型！',
        'group_type.require' => '请选择用户组类型！',
        'group_type.number' => '请选择用户组类型！',
        'group_type.in' => '请选择用户组类型！',
        'group_admin.require' => '请选择是否允许后台登录！',
        'group_admin.number' => '请选择是否允许后台登录！',
        'group_admin.in' => '请选择是否允许后台登录！',
        'group_up.require' => '请填写用户升级积分！',
        'group_up.number' => '请填写用户升级积分！',
        'sort.require' => '排序值不得为空！',
        'sort.number' => '排序值只能为数字！',
        'group_space.require' => '用户可用空间不得为空！',
        'group_space.number' => '用户可用空间填写错误！',

        '__token__.token' => '已超时，请刷新页面',
    ];
    protected $scene = [
        'add' => ['pid','title','status','group_up','group_money','group_type','group_admin','group_space','sort'],
        'edit' => ['id','title','status','grouptype','groupadmin','groupup','sort'],
        'fastedit' => ['id','sort'],
        'auhtgroupauth' => ['id','cl','groupauth','__token__'],
    ];
}