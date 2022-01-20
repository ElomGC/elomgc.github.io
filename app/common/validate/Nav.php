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

class Nav extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "pid|上级分类" => "number",
        "title|分类名称" => "require|min:2|max:80|regex:^[^%#&'$]*$",
        "status|启用状态" => "require|number|in:0,1",
        "target|打开方式" => "require|number|in:0,1",
        "sort|排序值" => "require|number",
        "uri|链接地址" => "require",
    ];
    protected $scene = [
        'add' => ['title','status','sort','uri'],
        'edit' => ['id','title','status','sort','uri'],
        'fastedit' => ['id'],
    ];
    public function sceneSpecialclassadd(){
        return $this->only(['title','pid','status','sort']);
    }
    public function sceneSpecialclassedit(){
        return $this->only(['id','pid','title','status','sort']);
    }
}