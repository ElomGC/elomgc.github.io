<?php
declare(strict_types = 1);

namespace app\common\validate;


use think\Validate;

class Special extends Validate
{
    protected $rule =   [
        "id" => "require|number",
        "cid|分类" => "require|number",
        "title|标题" => "require|max:80",
        "status|启用状态" => "require|number",
        "keywords|关键词" => "regex:^[^%@#&',;$]*$",
        "description|简介" => "regex:^[^%@#&',;$]*$",
        "banber|横幅广告" => "regex:^[^%@#&',;$]*$",
        "logo|LOGO" => "regex:^[^%@#&',;$]*$",
        "temp_late|风格" => "regex:^[^%@#&',;$]*$",
        "temp_head|自订义头部" => "regex:^[^%@#&',;$]*$",
        "temp_list|自订义模板" => "regex:^[^%@#&',;$]*$",
        "temp_foot|自订义尾部" => "regex:^[^%@#&',;$]*$",
        "sort|排序" => "require|number",
        ];
    protected $scene = [
        'add' => ['cid','title','status','keywords','description','banber','logo','temp_late','temp_head','temp_list','temp_foot','sort'],
        'edit' => ['id','cid','title','status','keywords','description','banber','logo','temp_late','temp_head','temp_list','temp_foot','sort'],
        ];


}