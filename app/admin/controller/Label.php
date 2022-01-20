<?php
declare(strict_types = 1);
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\ModelList;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\exception\ValidateException;
use think\facade\View;
use worm\NodeFormat;

class Label extends AdminBase {
    use AddEditList;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\WormEdit";
        $this->validate = "app\\common\\validate\\WormEdit";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
    }
    public function index(){
        if($this->getdata['id'] == '0'){
            $this->getdata = Common::del_null($this->getdata);
            $add = $this->model->setOne($this->getdata);
            $this->redirect(url('type',['id' => $add['id']])->build());
        }
        if(empty($this->getdata['type'])){
            $this->redirect(url('type',['id' => $this->getdata['id']])->build());
        }
    }
    public function type(ModelList $modelList){
        $this->list_temp = 'type';
        $listdb = $modelList->getList(['class' => '0','status' => '1'],true);
        View::assign(['postdb' => $this->getdata]);
        return $this->viewAdminList($listdb);
    }
    //  调用主栏目单篇
    public function partone(ModelList $modelList){
        $old = $this->getOld();
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'partone';
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            $this->delOldimg($old);
            if($this->model->setOne($data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $_modellist = $modelList->getList(['id' => (string)$old['moid']],true);
        if(empty($_modellist['0'])){
            $this->error("模块不存在或已禁用");
        }
        $_modellist = $_modellist['0'];
        $modellist = $this->model->getModellist($_modellist);
        $partlist['mid'] = array_column($modellist,'id');
        $partlist['class'] = '1';
        $partlist['keys'] = $_modellist['keys'];
        $partlist = $this->model->getPartlist($partlist);
        $fileList = $this->getFile('part');
        $this->list_base['title'] = "调用-{$_modellist['title']}-单篇文章";
        View::assign([
            'list_base' => $this->list_base,
            'modellist' => $modellist,
            'partlist' => $partlist,
            'fileList' => $fileList,
            'postdb' => $old,
        ]);
        return view();
    }
    //  调用主栏目列表
    public function partlist(ModelList $modelList){
        $old = $this->getOld();
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'partlist';
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            $this->delOldimg($old);
            if($this->model->setOne($data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $_modellist = $modelList->getList(['id' => (string)$old['moid']],true);
        if(empty($_modellist['0'])){
            $this->error("模块不存在或已禁用");
        }
        $_modellist = $_modellist['0'];
        $modellist = $this->model->getModellist($_modellist);
        $partlist['mid'] = array_column($modellist,'id');
        $partlist['class'] = 'a';
        $partlist['keys'] = $_modellist['keys'];
        $partlist = $this->model->getPartlist($partlist);
        $fileList = $this->getFile('part');
        $this->list_base['title'] = "调用-{$_modellist['title']}-栏目列表";
        View::assign([
            'list_base' => $this->list_base,
            'modellist' => $modellist,
            'partlist' => $partlist,
            'fileList' => $fileList,
            'postdb' => $old,
        ]);
        return view();
    }
    //  模型主栏目调用
    public function partedit(ModelList $modelList){
        $old = $this->getOld();
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'partedit';
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            $this->delOldimg($old);
            if($this->model->setOne($data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $_modellist = $modelList->getList(['id' => (string)$old['moid']],true);
        if(empty($_modellist['0'])){
            $this->error("模块不存在或已禁用");
        }
        $_modellist = $_modellist['0'];
        $modellist = $this->model->getModellist($_modellist);
        $partlist['mid'] = array_column($modellist,'id');
        $partlist['class'] = '0';
        $partlist['keys'] = $_modellist['keys'];
        $partlist = $this->model->getPartlist($partlist);
        $fileList = $this->getFile('part');
        $this->list_base['title'] = "调用-{$_modellist['title']}-主栏目内容";
        View::assign([
            'list_base' => $this->list_base,
            'modellist' => $modellist,
            'partlist' => $partlist,
            'fileList' => $fileList,
            'postdb' => $old,
        ]);
        return view();
    }
    //  文本文档
    public function labelcktext(){
        $this->list_base['title'] = '编辑-富文本编辑器';
        $this->list_base['add'] = true;
        $this->list_temp = 'label:labeltext';
        $old = $this->getOld(false);
        $this->list_base['uri'] = url('labelcktext',['id' => $old['id']])->build();
        $this->list_base['resurl'] = url('type',['id' => $old['id']])->build();
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'labelcktext';
            $img_data = [
                'oldimg' => $old['type'] != 'labelcktext' ? null : $old['conf'],
                'newimg' => $data['conf'],
            ];
            $data['conf'] = Upload::setEditOr($img_data, date('Y-m', time()));
            $data['conf'] = Upload::editadd($data['conf']);
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            if(in_array($old['type'],['labelvideo','labelimgs'])){
                $this->delOldimg($old);
            }
            if($this->model->setOne($data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        if(!in_array($old['type'],['labeltext','labelcktext'])){
            $old['conf'] = '';
        }
        $this->list_base['url'] = url('type',['id' => $old['id']])->build();
        $this->form_list = [
            ['file' => 'title','title' => '标签名','type' => 'text','default' => $old['name'],],
            ['file' => 'conf','title' => '内容','type' => 'editor'],
            ['file' => 'status','title' => '启用','type' => 'radio','data' => ['list' => ['1' => '正常显示','0' => '暂时隐藏'],'default' => '1']],
            ['file' => 'moid','title' => '启用','type' => 'text','type_edit' => 'hidden','default' => '0'],
            ['file' => 'id','title' => '启用','type' => 'text','type_edit' => 'hidden'],
        ];
        return $this->viewAdminAdd($old);
    }
    public function labeltext(){
        $this->list_base['title'] = '编辑-文本文档';
        $this->list_base['add'] = true;
        $old = $this->getOld(false);
        $this->list_temp = 'label:labeltext';
        $this->list_base['resurl'] = url('type',['id' => $old['id']])->build();
        $this->list_base['uri'] = url('labeltext',['id' => $old['id']])->build();
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'labeltext';
            $img_data = [
                'oldimg' => $old['type'] != 'labelcktext' ? null : $old['conf'],
                'newimg' => $data['conf'],
            ];
            $data['conf'] = Upload::setEditOr($img_data, date('Y-m', time()));
            $data['conf'] = Upload::editadd($data['conf']);
            $data['conf'] = json_encode($data['conf'],JSON_UNESCAPED_UNICODE);
            if(in_array($old['type'],['labelvideo','labelimgs'])){
                $this->delOldimg($old);
            }
            if($this->model->setOne($data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        if(!in_array($old['type'],['labeltext','labelcktext'])){
            $old['conf'] = '';
        }
        $this->form_list = [
            ['file' => 'title','title' => '标签名','type' => 'text','default' => $old['name'],],
            ['file' => 'conf','title' => '内容','type' => 'textarea','rows' => '8'],
            ['file' => 'status','title' => '启用','type' => 'radio','data' => ['list' => ['1' => '正常显示','0' => '暂时隐藏'],'default' => '1']],
            ['file' => 'moid','title' => '启用','type' => 'text','type_edit' => 'hidden','default' => '0'],
            ['file' => 'id','title' => '启用','type' => 'text','type_edit' => 'hidden'],
        ];
        return $this->viewAdminAdd($old);
    }
    //  视频 上传
    public function labelvideo(){
        $this->list_base['title'] = '编辑-视频上传';
        $this->list_base['tip'] = [
            'title' => '调用说明',
            'list' => ["1.  视频名称： \$rs['title'], 视频地址： \$rs['uri']","2.  默认模板： 多视频为循环显示","3.  排序为数字大的在前",]
        ];
        $this->list_base['add'] = true;
        $old = $_old = $this->getOld(false);
        $this->list_temp = 'label:labeltext';
        $this->list_base['resurl'] = url('type',['id' => $old['id']])->build();
        if($old['type'] != 'labelvideo'){
            $old['conf'] = '';
        }else{
            $old['conttemp'] = empty($old['conf']['conttemp']) ? '' : $old['conf']['conttemp'];
            $old['conf'] = empty($old['conf']['videolist']) ? [] : $old['conf']['videolist'];
        }
        $this->form_list = [
            ['file' => 'title','title' => '标签名','type' => 'text','default' => $old['name'],],
            ['file' => 'conf','title' => '视频','type' => 'upload','type_edit' => 'array','upload_accept' => 'video'],
            ['file' => 'status','title' => '启用','type' => 'radio','data' => ['list' => ['1' => '正常显示','0' => '暂时隐藏'],'default' => '1']],
            ['file' => 'conttemp','title' => '模板代码','type' => 'textarea','rows' => '8','tip' => "如不填写将显示默认模板"],
            ['file' => 'moid','title' => '启用','type' => 'text','type_edit' => 'hidden','default' => '0'],
            ['file' => 'id','title' => '启用','type' => 'text','type_edit' => 'hidden'],
        ];
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $data['type'] = 'labelvideo';
            $_data = Common::SetReadFile($this->form_list,$data,$old);
            if($_data['code'] != '1'){
                $this->error($_data['msg']);
            }
            $_data = array_merge($data,$_data['data']);
            $_data['conf'] = ['videolist' => Upload::editadd($_data['conf']),'conttemp' => $_data['conttemp'],];
            $_data['conf'] = json_encode($_data['conf'],JSON_UNESCAPED_UNICODE);
            if(in_array($old['type'],['labelcktext','labelimgs','labeltext'])){
                $this->delOldimg($_old);
            }
            if($this->model->setOne($_data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $this->list_base['uri'] = url('labelvideo',['id' => $old['id']])->build();
        return $this->viewAdminAdd($old);
    }
    //  图片上传
    public function labelimgs(){
        $this->list_base['title'] = '编辑-图片上传';
        $this->list_base['tip'] = [
            'title' => '调用说明',
            'list' => ["1.  图片名称： \$rs['title'], 图片地址： \$rs['uri'], 链接地址： \$rs['url']","2.  默认模板： 多张图片为循环显示","3.  排序为数字大的在前",]
        ];
        $this->list_base['add'] = true;
        $old = $_old = $this->getOld(false);
        $this->list_temp = 'label:labeltext';
        $this->list_base['resurl'] = url('type',['id' => $old['id']])->build();
        if($old['type'] != 'labelimgs'){
            $old['conf'] = '';
        }else{
            $old['conttemp'] = empty($old['conf']['conttemp']) ? '' : $old['conf']['conttemp'];
            $old['conf'] = empty($old['conf']['imglist']) ? [] : $old['conf']['imglist'];
        }
        $this->form_list = [
            ['file' => 'title','title' => '标签名','type' => 'text','default' => $old['name'],],
            ['file' => 'conf','title' => '图片','type' => 'upload','upload_accept' => 'image','type_edit' => 'array','like' => true],
            ['file' => 'status','title' => '启用','type' => 'radio','data' => ['list' => ['1' => '正常显示','0' => '暂时隐藏'],'default' => '1']],
            ['file' => 'conttemp','title' => '模板代码','type' => 'textarea','rows' => '8','tip' => "如不填写将显示默认模板"],
            ['file' => 'moid','title' => '启用','type' => 'text','type_edit' => 'hidden','default' => '0'],
            ['file' => 'id','title' => '启用','type' => 'text','type_edit' => 'hidden'],
        ];
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            try {
                validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $_data = Common::SetReadFile($this->form_list,$data,$old);
            $data['type'] = 'labelimgs';
            if($_data['code'] != '1'){
                $this->error($_data['msg']);
            }
            $_data = array_merge($data,$_data['data']);
            $_data['conf'] = ['imglist' => Upload::editadd($_data['conf']),'conttemp' => $_data['conttemp'],];
            $_data['conf'] = json_encode($_data['conf'],JSON_UNESCAPED_UNICODE);
            if(in_array($old['type'],['labelcktext','labeltext','labelvideo'])){
                $this->delOldimg($_old);
            }
            if($this->model->setOne($_data)){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $this->list_base['uri'] = url('labelimgs',['id' => $old['id']])->build();
        return $this->viewAdminAdd($old);
    }
    //  读取现有数据
    protected function getOld($moid = true){
        $old = $this->model->getList(['id' => $this->getdata['id']]);
        $old = $old['0'];
        $old['moid'] = empty($this->getdata['moid']) ? $old['moid'] : $this->getdata['moid'];
        if($moid && empty($old['moid'])){
            $this->redirect(url('type',['id' => $old['id']])->build());
        }
        $old['conf'] = empty($old['conf']) ? [] : json_decode($old['conf'],true);
        $old['conf'] = in_array($old['type'],['labelimgs','labelvideo','labeltext','labelcktext']) ? Upload::editadd($old['conf'],false) : $old['conf'];
        return $old;
    }
    //  读取模板文件
    protected function getFile($dir){
        $fileList = get_dir_file(root_path("public/label/{$dir}"),'jpg');
        if(!empty($fileList)){
            foreach ($fileList as $k => $v){
                $name = basename($v,'.jpg');
                $name = explode('_',$name);
                $_v = [
                   'img' => str_replace(root_path(),"/",$v),
                   'cnname' => $name['0'],
                   'enname' => empty($name['1']) ? $name['0'] : $name['1'],
                ];
                $fileList[$k] = $_v;
            }
        }
        return $fileList;
    }
    //  删除图片文件
    protected function delOldimg($data){
        switch ($data['type']){
            case 'labelvideo':
                $imgs = empty($data['conf']['videolist']) ? [] : array_column($data['conf']['videolist'],'uri');
                break;
            case 'labelimgs':
                $imgs = empty($data['conf']['imglist']) ? [] : array_column($data['conf']['imglist'],'uri');
                break;
            case 'labelcktext':
                $imgs = empty($data['conf']) ? [] : Upload::img_list($data['conf']);
                break;
            case 'labeltext':
                $imgs = empty($data['conf']) ? [] : Upload::img_list($data['conf']);
                break;
        }
        if(!empty($imgs)){
            Upload::fileDel($imgs);
        }
        return true;
    }
    //  读取模板文件
    public function getView(){
        $data = Common::data_trim(input('post.'));
        if(!is_file(root_path('public/label').$data['data'])){
            $this->error("模板不存在");
        }
        $fileList = read_file(root_path('public/label').$data['data']);
        $this->result($fileList,'1','查询成功');
    }
}