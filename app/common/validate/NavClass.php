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

class NavClass extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "title|分类名称" => "require|min:2|max:80|regex:^[^%#&'$]*$",
        "status|启用状态" => "require|number|in:0,1",
        "sort|排序值" => "require|number",
        "level|二级导航" => "number",
    ];
    protected $scene = [
        'add' => ['title','status','sort','level'],
        'edit' => ['id','title','status','sort','level'],
        'fastedit' => ['id','sort'],
    ];
}