<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class ArtComment extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "pid|回复ID" => "number",
        "aid|文章" => "require|number",
        "uid|用户" => "require|number",
        "jian|推荐" => "number",
        "type|类型" => "require",
        "content|内容" => "require",
        "status|是否启用" => "number",
    ];
    protected $message  =   [
         'id.require'          => '非法访问',
         'id.number'          => '非法访问',
        ];
    protected $scene = [
        'add' => ['pid','aid','uid','jian','type','status'],
    ];
}