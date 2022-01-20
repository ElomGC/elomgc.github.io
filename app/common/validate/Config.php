<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class Config extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "title" => "require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u",
        "conf" => "require|alphaDash",
        "conf_value" => "regex:^[^%#&'$]*$",
        "class" => "require|alphaNum",
        "form_type" => "require|alphaDash",
        "form_required" => "require|number|in:0,1",
        "status" => "require|number|in:0,1,2",
        "sort" => "require|number",
    ];
    protected $message  =   [
        'id.require'          => '非法访问',
        'id.number'          => '非法访问',
        'title.require'        => '名称不得为空',
        'title.max'        => '名称不得超过80字符',
        'title.regex'        => '模块名称不得输入特殊字符',
        'conf.require'        => '参数键不得为空',
        'conf.alphaDash'        => '参数键只能是小写字母',
        'conf_value.regex'        => '配置不得输入特殊字符',
        'class.require'        => '参数分类不得为空',
        'class.alphaNum'        => '参数分类选择错误',
        'form_type.require'        => '表单类型不得为空',
        'form_type.alpha'        => '表单类型选择错误',
        'form_required.require'        => '请选择是否必填',
        'form_required.number'        => '请选择是否必填',
        'form_required.in'        => '请选择是否必填',

        'status.require' => '请选择是否启用！',
        'status.number' => '请选择是否启用！',
        'status.in' => '请选择是否启用！',

        'sort.require' => '请填写排序值！',
        'sort.number' => '排序值填写错误！',
    ];
    protected $scene = [
        'add' => ["title","conf","class","form_type","form_required","status","sort"],
        'edit' => ["id","title","conf","class","form_type","form_required","status","sort"],
        'fastedit' => ['id','sort' => 'number']
    ];
}