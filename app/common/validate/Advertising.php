<?php
declare(strict_types = 1);

namespace app\common\validate;
use think\Validate;
class Advertising extends Validate{
    protected $rule =   [
        'title'  => 'require',
        'status'  => 'require|number',
        'class'  => 'require|number',
        'id'  => 'require|number',
    ];

    protected $message  =   [
        'title.require' => '广告名称不得为空！',
        'status.require' => '请选择是否启用！',
        'status.number' => '是否启用选择错误！',
        'class.require' => '请选择广告类型！',
        'class.number' => '广告类型选择错误！',
        '__token__.token' => '已超时，请重试！',

    ];
    protected $scene = [
        'add' => ['title','status','class'],
        'edit' => ['title','status','class','id'],
    ];
}