<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\View;

class Model extends AdminBase {
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\ModelList";
        $this->validate = "app\\common\\validate\\ModelList";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->list_base['idsee'] = false;
        $this->list_base['add'] = false;
        $this->getConf();
    }
    protected function getMap()
    {
        $map['class'] = empty($this->getdata['class']) ? '0' : $this->getdata['class'];
        $map['status'] = empty($this->getdata['status']) ? 'a' : $this->getdata['status'];
        return array_merge( parent::getMap(),$map);
    }

    protected function getConf(){
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center'],
            ['file' => 'title','title' => '模块名称','type' => 'text','width' => '30%'],
            ['file' => 'name','title' => '模块组','type' => 'text','width' => '30%'],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1'],
            ['filed' => 'read','title' => '查看','type' => 'btn','text' => true,'open' => true,'opentitle' => "查看模块",'uri' => url('edit',['id' => '__id__','class' => '__class__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80']
        ];
        if($this->request->action() == 'index'){
            $this->list_base['uri'] = url('getlist',['class' => $this->getdata['class']])->build();
        }else if($this->request->action() == 'edit'){
            //  开始生成表单参数
            $this->form_list = [
                ['file' => 'title','title' => '模块名称','type' => 'text','disabled' => true,],
                ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number'],
                ['file' => 'name','title' => '模块组名称','type' => 'text','disabled' => true,],
                ['file' => 'status','title' => '状态','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '1')],
            ];
        }
    }
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
            return $this->error($e->getError());
        }
        $data = $this->model->getEditAdd($data);
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功",url('authrule/index')->build(),$add);
        }
        $this->error("{$res_title}失败");
    }
    //  复制模块
    public function copymodel(){
        if(empty($this->getdata['id'])){
            $this->error("请选择要复制的模块");
        }
        $this->list_temp = 'copymodel';
        $data = $this->model->whereId($this->getdata['id'])->find();
        $this->list_base['uri'] = url('model/copyend',['class' => $this->getdata['class']]);
        $this->list_base['title'] = '复制模块';
        $data['title'] = $data['title'].'_copy';
        $data['name'] = $data['name'].'_copy';
        //  开始生成表单参数
        $this->form_list = array(
            ['file' => 'title','title' => '模块新名称','type' => 'text','required' => true,],
            ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number'],
            ['file' => 'name','title' => '模块组新名称','type' => 'text','required' => true,],
            ['file' => 'status','title' => '状态','type' => 'radio','data' => array('list' => array('1' => '启用','0' => '禁用'),'default' => '1')],
        );
        return $this->viewAdminAdd($data);
    }
    //
    public function copyend(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        try {
            validate($this->validate)->scene("add")->check($data);
        }catch (ValidateException $e){
            return $this->error($e->getError());
        }
        $newdata = $this->model->getEditAdd($data);
        unset($data['id']);
        $data = $this->model->getEditAdd($data);
        echo View::fetch();
        $this->resview("开始复制模块..");
        $res = $this->model->copydir($newdata,$data);
        if($res['code'] == '0'){
            $this->error($res['msg']);
        }
        $this->resview("创建目录成功..");
        $this->resview("开始创建数据表..");
        $res = $this->model->copytable($newdata,$data);
        if($res['code'] == '0'){
            $this->error($res['msg']);
        }
        $this->resview("创建数据表成功..");
        $this->resview("开始创建文件..");
        $res = $this->model->copyfile($newdata,$data);
        $this->resview("创建文件成功..");
        $this->resview("开始创建权限控制..");
        $res = $this->model->copyauth($newdata,$data);
        $this->resview("权限控制创建完成..");
        $this->resview("开始创建配置文件..");
        $res = $this->model->copyconfig($newdata,$data);
        if($res['code'] == '0'){
            $this->error($res['msg']);
        }
        $this->resview("创建配置文件成功..");
        $this->model->setOne($data);
        $this->resview("模块复制成功");
    }
    //  返回页面数据
    public function resview($msg,$show = false){
        $show = $show ? "$('#seebtn').show();" : null;
        $res = "<script> {$show} $('#seelist').append(\"<li class='layout pad-a5'>{$msg}</li>\");</script>";
        echo $res;
    }

}