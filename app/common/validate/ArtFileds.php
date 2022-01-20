<?php


namespace app\common\validate;


use think\Validate;
class ArtFileds extends Validate {
protected $rule =   [
        'id'                    => 'require|number',
        'mid'                    => 'require|number',
        "form_title"            => 'require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u',
        "sql_file"              => "require|max:80|alphaDash",
        "sql_type"              => "require",
        "form_type"             => "require|in:text,bgccont,chinacode,parameter,callcont,map,addchina,bindmodel,rescont,textarea,radio,checkbox,select,editor,upload_img,upload_file,upload_video,upload_imgtc,upload_imgarr,upload_videoarr,upload_imgarrtc,upload_filearr,money,number,time,date,datetime,hidden,icon,",
        "form_required"         => "require|number|in:0,1,2",
        "form_required_list"    => "alpha",
        "form_text"             => "regex:^[^%@#&',;$]*$",
        "form_unit"             => "max:10",
        "form_group"            => "regex:^[^%@#&',;$]*$",
        "list_show"             => "require|number|in:0,1",
        "cont_show"             => "require|number|in:0,1",
        'status'                => 'require|number|in:0,1,2',
        'sort'                  => 'require|number',
    ];
    protected $message  =   [
         'id.require'          => '非法访问1',
         'id.number'          => '非法访问2',
         'mid.require'          => '非法访问3',
         'mid.number'          => '非法访问4',
        "form_title.require"            => '字段名称不得为空',
        "form_title.max"            => '字段名称不得超过80字符',
        "form_title.regex"            => '字段名称禁止输入特殊字符',

        "sql_file.require"              => "字段键值不得为空",
        "sql_file.max"              => "字段键值不得超过80字符",
        "sql_file.alphaDash"              => "字段键值只能是字母",

        "sql_type.require"              => "字段类型选择错误",
        "sql_type.in"              => "字段类型选择错误",
        "form_type.require"              => "表单类型选择错误",
        "form_type.in"              => "表单类型选择错误",
        "form_required.require"              => "请选择是否必填",
        "form_required.number"              => "请选择是否必填",
        "form_required.in"              => "请选择是否必填",
        "form_required_list.alpha"              => "验证规则选择错误",
        "form_text.regex"              => "提示文字不得输入特殊字符",
        "form_unit.max"              => "字段单位不得超过10字符",
        "form_group.regex"              => "自订义分组不得输入特殊字符",

        "list_show.require"              => "请选择是否列表显示",
        "list_show.number"              => "请选择是否列表显示",
        "list_show.in"              => "请选择是否列表显示",
        "cont_show.require"              => "请选择是否内容页显示",
        "cont_show.number"              => "请选择是否内容页显示",
        "cont_show.in"              => "请选择是否内容页显示",

        'status.require'        => '请选择是否启用',
        'status.number'        => '请选择是否启用',
        'status.in'        => '请选择是否启用',
        'sort.require'        => '请填写排序值',
        'sort.number'        => '排序值填写错误',
        ];
     protected $scene = [
        'add' => ['mid','form_title','sql_file','sql_type','form_type','form_required','form_required_list','form_text','form_unit','form_group','list_show','cont_show','status','sort'],
        'edit' => ['mid','form_title','sql_file','sql_type','form_type','form_required','form_required_list','form_text','form_unit','form_group','list_show','cont_show','status','sort','id'],
        'fastedit' => ['id','sort' => 'number']
    ];
    public function sceneUserfileadd(){
        return $this->only(['form_title','sql_file','sql_type','form_type','form_required','form_required_list','form_text','form_unit','form_group','list_show','cont_show','status','sort']);
    }
    public function sceneUserfileedit(){
        return $this->only(['id','form_title','sql_file','sql_type','form_type','form_required','form_required_list','form_text','form_unit','form_group','list_show','cont_show','status','sort']);
    }
}