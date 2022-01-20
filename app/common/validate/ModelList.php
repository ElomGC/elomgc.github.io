<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class ModelList extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "title" => "require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u",
        "name" => "require|lower",
        "status" => "require|number",
    ];
    protected $message  =   [
        'id.require'          => '非法访问',
        'id.number'          => '非法访问',
        'title.require'        => '模块名称不得为空',
        'title.max'        => '模块名称不得超过80字符',
        'title.regex'        => '模块名称不得输入特殊字符',
        'name.require'        => '模块组不得为空',
        'name.lower'        => '模块组只能是小写字母',
        'status.require' => '填写错误！',
        'status.number' => '填写错误！',

    ];
    protected $scene = [
        'add' => ["title","name","status",],
        'edit' => ["id","title","name","status",],
        'fastedit' => ['id','sort' => 'number']
    ];
}