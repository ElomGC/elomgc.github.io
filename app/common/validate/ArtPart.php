<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class ArtPart extends Validate {
    protected $rule =   [
        "id" => "require|number",
        "title" => "require|max:80",
        "class" => "require|number|in:0,1",
        "pid" => "number",
        "mid" => "require|number",
        "limit" => "number|requireIf:class,0",
        "title_num" => "number|requireIf:class,0",
        "cont_num" => "number|requireIf:class,0",
        "pid_see" => "number|in:0,1|requireIf:class,0",
        "comment_see" => "number|in:0,1|requireIf:class,0",
        "status" => "require|number|in:0,1",
        "keywords" => "regex:^[^%@#&',;$]*$",
        "temp_late" => "regex:^[^%@#&',;$]*$",
        "temp_head" => "regex:^[^%@#&',;$]*$",
        "temp_list" => "regex:^[^%@#&',;$]*$",
        "temp_cont" => "regex:^[^%@#&',;$]*$",
        "temp_foot" => "regex:^[^%@#&',;$]*$",
        "banber" => "regex:^[^%@#&',;$]*$",
        "logo" => "regex:^[^%@#&',;$]*$",
        'sort'      => 'number|requireIf:class,0',
        'order'      => 'requireIf:class,0',
    ];
    protected $message  =   [
         'id.require'          => '非法访问',
         'id.number'          => '非法访问',
        'title.require'        => '名称不得为空',
        'title.max'        => '名称不得超过80字符',
        'title.regex'        => '名称不得输入特殊字符',

         'class.require'          => '请选择栏目类型',
         'class.number'          => '请选择栏目类型',
         'class.in'          => '请选择栏目类型',

         'pid.number'          => '上级栏目选择错误',
         'mid.require'          => '请选择模型',
         'mid.number'          => '请选择模型',
         'limit.require'          => '列表显示条数不得为空',
         'limit.number'          => '列表显示条数填写错误',
         'title_num.require'          => '内容标题显示字数不得为空',
         'title_num.number'          => '内容标题显示字数填写错误',
         'cont_num.require'          => '内容简介显示字数不得为空',
         'cont_num.number'          => '内容简介显示字数填写错误',
         'pid_see.require'          => '请选择是否在上级栏目显示',
         'pid_see.number'          => '请选择是否在上级栏目显示',
         'comment_see.require'          => '请选择是否启用评论',
         'comment_see.number'          => '请选择是否启用评论',
         'status.require'          => '请选择是否启用栏目',
         'status.number'          => '请选择是否启用栏目',
         'jumpurl.activeUrl'          => '外部链接填写错误',
         'keywords.regex'          => '关键词不得填写特殊字符',
         'temp_late.regex'          => '自订义风格选择错误',
         'temp_head.regex'          => '自订义头部风格填写错误',
         'temp_list.regex'          => '自订义列表页风格填写错误',
         'temp_cont.regex'          => '自订义内容页风格填写错误',
         'temp_foot.regex'          => '自订义底部风格填写错误',
         'logo.regex'          => 'LOGO选择错误',
         'banber.regex'          => 'BANBER选择错误',
         'sort.require'          => '请填写排序值',
         'sort.number'          => '排序值填写错误',
          'order.require'      => '请选择排序方式',
        ];
    protected $scene = [
        'add' => ["title","class","pid","mid","limit","title_num","cont_num","pid_see","comment_see","status","jumpurl","keywords","temp_late","temp_head","temp_list","temp_cont","temp_foot",'sort',"logo","banber",'order'],
        'edit' => ["id","title","class","pid","mid","limit","title_num","cont_num","pid_see","comment_see","status","jumpurl","keywords","temp_late","temp_head","temp_list","temp_cont","temp_foot",'sort',"logo","banber",'order'],
        'fastedit' => ['id','sort' => 'number']
    ];
    public function sceneFuadd(){
        return $this->only(["title","pid","limit","title_num","cont_num","pid_see","status","jumpurl","keywords","temp_late","temp_head","temp_list","temp_cont","temp_foot",'sort',"logo","banber",'order']);
    }
    public function sceneFuedit(){
        return $this->only(["id","title","pid","limit","title_num","cont_num","pid_see","status","jumpurl","keywords","temp_late","temp_head","temp_list","temp_cont","temp_foot",'sort',"logo","banber",'order']);
    }
}