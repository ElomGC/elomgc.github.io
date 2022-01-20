<?php
declare(strict_types = 1);
namespace app\common\validate;

use think\Validate;
class Comment extends Validate {
    protected $rule =   [
        'status|状态'  => 'number|in:0,1,2',
        'pid'  => 'number',
        'jian|推荐'  => 'number|in:0,1',
        'id'  => 'require|number',
        'uid|用户名'  => 'require|number',
        'content|评论内容'  => 'require'
    ];

    protected $message  =   [
        'content.require' => '填写错误！',

        'pid.number' => '填写错误！',

        'id.require' => '填写错误！',
        'id.number' => '填写错误！',
    ];
    public function sceneAdd(){
        return $this->only(['uid','content']);
    }
    public function sceneEdit(){
        return $this->only(['status','pid','jian','id']);
    }
}