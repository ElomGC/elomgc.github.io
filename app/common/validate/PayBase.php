<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;

class PayBase extends Validate
{
    protected $rule =   [
        "oid|订单编号" => "require",
        "address_id|地址" => "require|number",
        "shoplist|商品" => "require|array",
        "model|模块" => "require",
        "aid|商品" => "require|number",
        "uid|用户" => "require|number",
    ];
    protected $scene = [
        'box' => ['address_id','shoplist','uid'],
        'boxedit' => ['oid','shoplist','uid'],
        'add' => ['model','aid'],
        'edit' => ['oid','model','aid'],
    ];
}