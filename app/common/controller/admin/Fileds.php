<?php
declare(strict_types = 1);

namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\model\UserGroup;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;

abstract class Fileds extends AdminBase {
    use AddEditList;
    protected $basename;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['3'];
        //  定义模型
        if($this->request->action() == 'index'){
            if(empty($this->getdata['mid'])){
                $this->error("非法访问");
            }
            $this->list_base['uri'] = url('getlist',['mid' => $this->getdata['mid']])->build();
            $this->list_base['add'] = ['title' => '添加字段', 'class' => 'cx-button-s cx-bg-green', 'uri' => url('create',['mid' => $this->getdata['mid']])->build()];
            $this->list_top = [['title' => '模型列表', 'class' => 'cx-button-s cx-bg-yellow cx-mag-r10','uri' => url($this->basename.'.artmodel/index')->build()]];
        }
        $mdodel = "app\\common\\model\\".$this->basename."\\".ucfirst($array['0']['4']);
        $this->model = new $mdodel;
        $this->validate = "app\\common\\validate\\ArtFileds";
        $this->list_base['page'] = '2';
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        $this->list_file = [
            ['file' => 'form_title','title' => '字段名','type' => 'text','width' => '30%','fixed' => 'left',],
            ['file' => 'sql_file','title' => '键名','type' => 'text','width' => '25%'],
            ['file' => 'form_type','title' => '表单类型','type' => 'radio','data' => ["text" => "单行文本框","textarea" => "多行文本框","radio" => "单选按钮","checkbox" => "多选按钮","select" => "下拉菜单","editor" => "富文本编辑器","upload_img" => "单张图片","upload_file" => "单个文件","upload_video" => "单视频上传","upload_imgtc" => "单张图片带文字带链接","upload_imgarr" => "多张图片","upload_videoarr" => "多视频上传","upload_imgarrtc" => "多张图片带文字带链接","upload_filearr" => "多文件上传","money" => "金额","number" => "数字","time" => "时间","date" => "日期","datetime" => "日期+时间","hidden" => "隐藏域","icon" => "字体图标","chinacode" => "系统地区","multiparameter" => "自订义参数",],'width' => '150'],
            ['file' => 'sort','title' => '排序','type' => 'edit','class' => 'cx-text-center','class_value' => 'cx-text-center','width' => '80',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '启用|禁用','textalign' => 'center','width' => '100','default' => '1'],
        ];
        if($this->contauth['edit']){
            $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑参数分类",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
        }
        if($this->contauth['del']){
            $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
        }
        if(in_array($this->request->action(),['edit','create'])) {
            $this->list_base['uri'] = url('save',$this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $usermodel = new UserGroup();
            $group_list = $usermodel->getList();
            $new_group_list = [];
            if(!empty($group_list)){
                foreach ($group_list as $k => $v){
                    $new_group_list[$v['id']] = $v['title'];
                }
            }
             $this->form_list = [
                 ['file' => 'form_title','title' => '字段名称','type' => 'text','required' => true,],
                 ['file' => 'sql_file','title' => '字段键值','type' => 'text','disabled' => $this->request->action() == 'edit' ? true : false,'tip' => '只能是字母或字母加数字,并且禁止修改','required' => true],
                 ['file' => 'sql_type','title' => '字段类型','type' => 'select','disabled' => $this->request->action() == 'edit' ? true : false,'data' => ['list' => ["varchar(255) DEFAULT NULL" => "255个字符串以内","int(11) NOT NULL DEFAULT '0'" => "10位以内纯数字","tinyint(2) NOT NULL DEFAULT '0'" => "2位以内纯数字","decimal(10,2) unsigned NOT NULL DEFAULT '0'" => "价格","text DEFAULT NULL" => "常用文本文档","mediumtext DEFAULT NULL" => "大型文本文档",],'default' => ''],'tip' => '选择后禁止修改，容易出错','required' => true],
                 ['file' => 'form_type','title' => '表单类型','type' => 'select','data' => ['list' => ["text" => "单行文本框","textarea" => "多行文本框","radio" => "单选按钮","checkbox" => "多选按钮","select" => "下拉菜单","editor" => "富文本编辑器","upload_img" => "单张图片","upload_file" => "单个文件","upload_video" => "单视频上传","upload_imgtc" => "单张图片带文字带链接","upload_imgarr" => "多张图片","upload_videoarr" => "多视频上传","upload_imgarrtc" => "多张图片带文字带链接","upload_filearr" => "多文件上传","money" => "金额","number" => "数字","time" => "时间","date" => "日期","datetime" => "日期+时间","hidden" => "隐藏域","icon" => "字体图标","chinacode" => "系统地区模块","multiparameter" => "自订义参数"],'default' => ''],'required' => true,'file_link' => ['radio' => ['form_data'],'checkbox' => ['form_data'],'select' => ['form_data'],'bgccont' => ['form_geturi','form_geturitype','form_js'],'callcont' => ['form_geturi','form_geturitype','form_js'],'bindmodel' => ['form_geturi','form_geturitype']]],
                 ['file' => 'form_required','title' => '是否必填','type' => 'radio','data' => ['list' => ['1' => '必填项','0' => '非必填'],'default' => '0'],'file_link' => ['1' => ['form_required_list']]],
                 ['file' => 'form_required_list','title' => '验证规则','type' => 'select','data' => ['list' => ['phone' => '手机验证','email' => '邮箱验证','url' => '网址验证','number' => '数字验证','date' => '日期验证','identity' => '身份证验证',],'default' => ''],'tip' => "非必填项，本项无效"],
                 ['file' => 'form_text','title' => '提示文字','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_unit','title' => '字段单位','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_default','title' => '默认值','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_geturi','title' => '数据请求地址','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_js','title' => '自订义变量','type' => 'textarea','tip' => '一般为空'],
                 ['file' => 'form_geturitype','title' => '聚合类型','type' => 'radio','data' => ['list' => ['1' => '单选','0' => '多选'],'default' => '0']],
                 ['file' => 'form_class','title' => '自订义样式','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_data','title' => '可选值','type' => 'textarea','tip' => '如果存在键值请用|进行分割，如“1|确定”，每行一个选项'],
                 ['file' => 'form_tip','title' => '提示语','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_group','title' => '自订义分组','type' => 'text','tip' => '一般为空'],
                 ['file' => 'form_edit','title' => '禁止编辑','type' => 'radio','data' => ['list' => ['1' => '禁止编辑','0' => '启用编辑'],'default' => '0']],
                 ['file' => 'admin_list_show','title' => '后台列表页显示','type' => 'radio','data' => ['list' => ['1' => '显示','0' => '隐藏'],'default' => '0']],
                 ['file' => 'list_show','title' => '前台列表页显示','type' => 'radio','data' => ['list' => ['1' => '显示','0' => '隐藏'],'default' => '0']],
                 ['file' => 'cont_show','title' => '前台内容页显示','type' => 'radio','data' => ['list' => ['1' => '显示','0' => '隐藏'],'default' => '1']],
                 ['file' => 'group_see','title' => '允许查看','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => ''],],
                 ['file' => 'group_edit','title' => '允许编辑','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => '']],
                 ['file' => 'setstatus','title' => '自动审核','type' => 'radio','data' => ['list' => ['1' => '自动审核','0' => '手动审核'],'default' => '1']],
                 ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['1' => '启用','0' => '禁用'],'default' => '1']],
                 ['file' => 'sort','title' => '排序','type' => 'text','default' => '0',],
                 ['file' => 'mid','title' => '模型ID','type' => 'text','type_edit' => 'hidden','tip' => '一般为空','required' => true,'required_list' => 'number','default' => empty($this->getdata['mid']) ? null : $this->getdata['mid']],
             ];
             if($this->request->action() == 'edit'){
                $phsh = [
                    ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]
                ];
               $this->form_list = array_merge($this->form_list,$phsh);
            }
         }
    }
    protected function getOrder(){
        return "sort desc,id asc";
    }
    //  保存数据
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if($this->request->isPut() && empty($data['id'])){
            $this->error("非法访问");
        }
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        }catch (ValidateException $e){
             $this->error($e->getError());
        }
        $data = $this->model->getEditAdd($data);
        if(empty($data['id']) && !$this->model->checkFiled($data)){
             $this->error("字段已存在或为保护字段");
        }
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功",'',$add);
        }
        $this->error("{$res_title}失败");
    }

}