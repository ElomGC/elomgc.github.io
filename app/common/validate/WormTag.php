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

class WormTag extends Validate {
    protected $rule =   [
        "id|ID" => "require|number",
        "title|标签名称" => "regex:^[^%#&'$]*$",
        "TagMid|模型" => "array",
        "TagFid|栏目" => "array",
        "TagLimit|显示数量" => "number",
        "status|启用状态" => "require|number|in:0,1",
    ];
    public function sceneAdd(){
        return $this->only(['id','title','TagMid','TagFid','TagLimit','status']);
    }
}