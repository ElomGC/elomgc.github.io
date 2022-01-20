<?php
declare(strict_types = 1);
namespace app\common\validate;

use think\Validate;
class ArtModel extends Validate {
    protected $rule =   [
        'id'          => 'require|number',
        'title'        => 'require|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u',
        'futitle'        => 'max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9-\_\s·]+$/u',
        'see_group'        => 'array',
        'edit_group'        => 'array',
        'status'      => 'require|number',
        'see_picurl'      => 'require|number',
        'see_keyword'      => 'require|number',
        'see_description'      => 'require|number',
        'see_comment'      => 'require|number',
        'see_add'      => 'require|number',
        'sort'      => 'require|number',
    ];
    protected $message  =   [
         'id.require'          => '非法访问',
         'id.number'          => '非法访问',
        'title.require'        => '模型名称不得为空',
        'title.max'        => '模型名称不得超过80字符',
        'title.regex'        => '模型名称不得输入特殊字符',
        'futitle.max'        => '模型别名不得超过80字符',
        'futitle.regex'        => '模型别名不得输入特殊字符',

        'see_group.array'        => '允许查看用户组选择错误',
        'edit_group.array'        => '允许编辑用户组选择错误',
        'status.require'        => '请选择是否启用',
        'status.number'        => '请选择是否启用',
        'sort.require'        => '请填写排序值',
        'sort.number'        => '排序值填写错误',
        'see_picurl.require'        => '请选择是否启用缩略图',
        'see_picurl.number'        => '请选择是否启用缩略图',
        'see_keyword.require'        => '请选择是否启用SEO关键词',
        'see_keyword.number'        => '请选择是否启用SEO关键词',
        'see_description.require'        => '请选择是否启用SEO简介',
        'see_description.number'        => '请选择是否启用SEO简介',
        'see_comment.require'        => '请选择是否启用评论',
        'see_comment.number'        => '请选择是否启用评论',
        'see_add.require'        => '请选择是否启用投稿',
        'see_add.number'        => '请选择是否启用投稿',
        ];
    protected $scene = [
        'add' => ['title','futitle','see_group','edit_group','status','sort','see_picurl','see_keyword','see_description','see_comment','see_add'],
        'edit' => ['title','futitle','see_group','edit_group','status','sort','id','see_picurl','see_keyword','see_description','see_comment','see_add'],
        'fastedit' => ['id','title' => 'max:80','sort' => 'number']
    ];
    public function sceneFormadd(){
        return $this->only(['title','see_group','add_group','status','sort']);
    }
    public function sceneFormedit(){
        return $this->only(['id','title','see_group','add_group','status','sort']);
    }
}