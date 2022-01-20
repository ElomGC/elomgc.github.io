<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Coupon extends Validate
{
    protected $rule =   [
        "id" => "require|number",
        "title|名称" => "require|max:80",
        "zonenum|发放总量" => "require|number",
        "class|优惠形式" => "require|number|in:0,1",
        "class_type|金额折扣" => "require|number",
        "type|使用门槛" => "require|number|in:0,1",
        "minmoney|满减金额" => "number",
        "group|投放用户组" => "array",
        "onelimit|每人限领" => "require|number",
        "time_type|有效期" => "require|number|in:0,1",
        "time_num|有效期" => "number",
        "article_limit|投放商品" => "require|number|in:0,1",
        "status|状态" => "require|number|in:0,1",
        "sort|排序" => "require|number",
    ];
    protected $message  =   [
        'id.require'          => '非法访问',
        'id.number'          => '非法访问',
        ];
    protected $scene = [
        'add' => ["title","zonenum","class","class_type","type","minmoney","group","onelimit","time_type","time_num","article_limit","article_list","status","sort"],
        'edit' => ["id","title","zonenum","class","class_type","type","minmoney","group","onelimit","time_type","time_num","article_limit","article_list","status","sort"],
        'fastedit' => ['id','sort' => 'number']
    ];
}