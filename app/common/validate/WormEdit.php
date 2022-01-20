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

class WormEdit extends Validate {
    protected $rule =   [
        "id|ID" => "require|number",
        "title|标签模板" => "regex:^[^%#&'$]*$",
        "conf|标签" => "require",
        "status|启用状态" => "require|number|in:0,1",
        "moid|模型" => "require|number",
    ];
    protected $scene = [
        'add' => ['title','conf','sort','moid'],
        'edit' => ['id','title','conf','sort','moid'],
        'fastedit' => ['id'],
    ];
}