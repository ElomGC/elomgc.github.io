<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\exception\ValidateException;

class Config extends AdminBase {
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\Config";
        $this->validate = "app\\common\\validate\\Config";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        //  定义验证器
        if(in_array($this->request->action(),['confedit','confeditsave'])) {
            if (empty($this->getdata['class'])) {
                $this->error("非法访问");
            }
            $conflist = $this->model->getConfedit($this->getdata);
            if ($conflist === false) {
                $this->error("还没有配置项，请先添加");
            }
            foreach ($conflist as $k => $v) {
                $res[] = Common::ReadFile($v);
            }
            $this->form_list = $res;
        } else {
            $this->getConf();
        }
    }

    protected function getConf(){
        $this->list_nav['list'] = $this->model->getConfigClass();
        foreach ($this->list_nav['list'] as $k => $v){
            $v['uri'] = url('config/index',['class' => $v['id']]);
            $this->list_nav['list'][$k] = $v;
        }
        $this->list_nav['default'] = empty($this->getdata['class']) ? $this->list_nav['list']['0']['id'] : $this->getdata['class'];
        $this->list_base['uri'] = url('getlist',['class' => $this->list_nav['default']])->build();
        $this->list_base['add'] = ['title' => '添加参数','class' => 'cx-button-s cx-bg-green','uri' => url('create',['class' => $this->list_nav['default']])->build()];
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','textalign' => 'center'],
            ['file' => 'title','title' => '参数','type' => 'edit','width' => '30%'],
            ['file' => 'conf','title' => '字段','type' => 'text','width' => '30%'],
            ['file' => 'sort','title' => '排序','type' => 'edit','textalign' => 'center','width' => '80',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1']
        ];
        if($this->contauth['edit']){
            $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑参数分类",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
        }
        if($this->contauth['del']){
            $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
        }
        $this->list_search = [
            'input' => ['name' => 'key'],
            'uri' => url('getlist',['class' => $this->list_nav['default']])->build(),
        ];
        if(in_array($this->request->action(),['edit','create'])) {
            $this->list_base['uri'] = url('config/save', $this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $this->list_base['title'] = false;
            $usermodel = new \app\common\model\User();
            $new_group_list['group_type'] = '0';
            $group_list = $usermodel->getAuthGroup($new_group_list);
            $new_group_list = [];
            if(!empty($group_list)){
                foreach ($group_list as $k => $v){
                    $new_group_list[$v['id']] = $v['title'];
                }
            }
            //  格式化权限类型选择
            $type_list = array();
            if(!empty($this->list_nav['list'])){
                foreach ($this->list_nav['list'] as $k => $v){
                    $type_list[$v['id']] = $v['title'];
                }
            }
            $this->form_list = [
                ['file' => 'class','title' => '参数分类','type' => 'select','required' => true,'data' => ['list' => $type_list,'default' => $this->list_nav['default'],'default_edit' => true]],
                ['file' => 'title','title' => '参数名称','type' => 'text','required' => true,],
                ['file' => 'conf','title' => '参数键','type' => 'text','required' => true,'tip' => '参数只能是小写字母','disabled' => $this->request->action() == 'edit' ? true : false],
                ['file' => 'form_type','title' => '表单类型','type' => 'select','data' => ['list' => ["text" => "单行文本框","textarea" => "多行文本框","radio" => "单选按钮","checkbox" => "多选按钮","select" => "下拉菜单","editor" => "富文本编辑器","upload_img" => "单张图片","upload_file" => "单个文件","upload_imgtc" => "单张图片带文字带链接","upload_imgarr" => "多张图片","upload_imgarrtc" => "多张图片带文字带链接","upload_filearr" => "多文件上传","money" => "金额","number" => "数字","time" => "时间","date" => "日期","datetime" => "日期+时间","hidden" => "隐藏域","icon" => "字体图标",],'default' => ''],'required' => true,'file_link' => ['radio' => ['form_data'],'checkbox' => ['form_data'],'select' => ['form_data']]],
                ['file' => 'form_required','title' => '是否必填','type' => 'radio','data' => ['list' => ['1' => '必填项','0' => '非必填'],'default' => '0'],'file_link' => ['1' => ['form_required_list']]],
                ['file' => 'form_required_list','title' => '验证规则','type' => 'select','data' => ['list' => ['phone' => '手机验证','email' => '邮箱验证','url' => '网址验证','number' => '数字验证','date' => '日期验证','identity' => '身份证验证',],'default' => ''],'tip' => "非必填项，本项无效"],
                ['file' => 'form_text','title' => '提示文字','type' => 'text','tip' => '一般为空'],
                ['file' => 'form_unit','title' => '字段单位','type' => 'text','tip' => '一般为空'],
                ['file' => 'form_default','title' => '默认值','type' => 'text','tip' => '一般为空'],
                ['file' => 'form_data','title' => '可选值','type' => 'textarea','tip' => '如果存在键值请用|进行分割，如“1|确定”，每行一个选项'],
                ['file' => 'form_tip','title' => '提示语','type' => 'text','tip' => '一般为空'],
                ['file' => 'group_see','title' => '允许查看','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => '',]],
                ['file' => 'group_edit','title' => '允许编辑','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => '']],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['1' => '启用','0' => '禁用'],'default' => '1']],
                ['file' => 'sort','title' => '排序','type' => 'text','default' => '0',],
            ];
            if($this->request->action() == 'edit'){
                $phsh = array(
                    ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
                );
                $this->form_list = array_merge($this->form_list,$phsh);
            }
        }
    }
    public function confedit(){
        $_cModel = new \app\common\model\ConfigClass();
        $_nav = $_cModel->whereId($this->getdata['class'])->find();
        $this->list_base['title'] = empty($_nav['title']) ? "配置{$this->getdata['class']}参数" : $_nav['title'];
        $this->list_base['uri'] = url('config/confeditsave',['class' => $this->getdata['class']]);
        $_webtemplate = Common::del_file($this->form_list,'file','web_template');
        $_webtemplate = empty($_webtemplate['0']) ? [] : $_webtemplate['0'];
        if(!empty($_webtemplate)){
            $_temp = getDirlist(root_path('view/home'));
            $_list = [];
            foreach ($_temp as $k => $v){
                $_v = root_path("view/home/{$v}")."{$v}.php";
                if(!is_file($_v)){
                    continue;
                }
                $_v = require $_v;
                $_list[$v] = $_v['name'];
            }
            $_webtemplate['type'] = 'select';
            $_webtemplate['default'] = empty($this->webdb['web_template']) ? 'default' : $this->webdb['web_template'];
            $_webtemplate['data'] = [
                'list' => $_list,
                'default' => empty($this->webdb['web_template']) ? 'default' : $this->webdb['web_template'],
                'default_edit' => true
            ];
            foreach ($this->form_list as $k => $v){
                if($v['file'] != 'web_template'){
                    continue;
                }
                $this->form_list[$k] = $_webtemplate;
                continue;
            }
        }
        return $this->viewAdminAdd($this->webdb);
    }
    public function confeditsave(){
        if(!$this->request->isPost()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        $_data = [];
        $fileList = array_keys($data);
        $fileList = $this->model->whereIn('conf',$fileList)->select()->toArray();
        foreach ($data as $k => $v){
            foreach ($fileList as $k1 => $v1){
                if($k == $v1['conf']){
                    $_data[$k] = $v1['conf_value'];
                }
            }
        }
        $_data = Upload::editadd($_data,false);
        $data = Common::SetReadFile($this->form_list,$data,$_data,'webdb');
        if($data['code'] == '0'){
            $this->error($data['msg']);
        }
        $_data = [];
        foreach ($data['data'] as $k => $v){
            foreach ($fileList as $k1 => $v1){
                if($k == $v1['conf']){
                    $v1['conf_value'] = $v;
                    $_data[] = $v1;
                }
            }
        }
        if($this->model->saveAll($_data)){
            cache('webdb',null);
            $this->success("保存成功");
        }
        $this->error('保存失败');
    }
}