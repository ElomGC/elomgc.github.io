<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class ArtArticle extends Validate {
protected $rule =   [
        'id'                    => 'require|number',
        'mid'                    => 'require|number',
        'uid'                    => 'require',
        'uuid'                    => 'require',
        'fid'                    => 'require|number',
    ];
    protected $message  =   [
         'id.require'          => '非法访问',
         'id.number'          => '非法访问',
         'mid.require'          => '请选择模型',
         'mid.number'          => '请选择模型',
         'fid.require'          => '请选择栏目',
         'fid.number'          => '请选择栏目',
         'uuid.require'          => '非法访问',
         'uid.require'          => '非法访问',
         'uid.number'          => '非法访问',
        ];
     protected $scene = [
        'add' => ['mid','uid','fid',],
        'edit' => ['id','mid','uid','fid',],
        'apiadd' => ['mid','uuid','fid',],
        'apiedit' => ['id','mid','uuid','fid',],
        'fastedit' => ['id','sort' => 'number']
    ];
    public function sceneFormadd(){
        return $this->only(['title','content','uid','mid']);
    }
    public function sceneFormedit(){
        return $this->only(['title','content','id','uid','mid']);
    }
    public function sceneFormhomeadd(){
        return $this->only(['title','content','mid']);
    }
    public function sceneFormhomeedit(){
        return $this->only(['title','content','id','mid']);
    }

}