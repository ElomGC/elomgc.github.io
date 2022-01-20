<?php
declare(strict_types = 1);
namespace app\admin\controller\form;

use app\common\controller\AdminBase;
use app\common\model\UserGroup;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;

class Artmodel extends AdminBase {
    use AddEditList;
    protected $basename;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\".$this->basename."\\FormModel";
        $this->validate = "app\\common\\validate\\ArtModel";
        $this->model = new $mdodel;
        $this->list_base['page'] = '2';
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf(){
        $this->list_file = [
            ['file' => 'id','title' => 'ID','type' => 'text','textalign' => 'center','width' => '80'],
            ['file' => 'title','title' => '表单类型','type' => 'link','uri' => url($this->basename.'.article/index',['mid'=>'__id__'])->build(),'width' => '40%'],
            ['file' => 'article_num','title' => '内容数量','width' => '120','textalign' => 'center','type' => 'link','uri' => url($this->basename.'.article/index',['mid'=>'__id__'])->build(),],
            ['file' => 'sort','title' => '排序','type' => 'edit','textalign' => 'center','width' => '80',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '审核|待审','textalign' => 'center','width' => '100','default' => '1'],
            ['filed' => 'filedlist','title' => '字段列表','type' => 'btn','text' => true,'uri' => url($this->basename.'.artmodelfile/index',array('mid'=>'__id__'))->build(),'icon' => 'cx-iconshezhi1 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '100']
        ];
        if($this->request->action() == 'index') {
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加表单类型', 'class' => 'cx-button-s cx-bg-green'];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑参数分类",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80'];
            }
        }else if($this->request->action() == 'edit' || $this->request->action() == 'create'){
            $this->list_base['uri'] = url('save',$this->request->action() == 'create' ? [] : ['id' => $this->getdata['id']]);
            $usermodel = new UserGroup();
            $group_list = $usermodel->getList([]);
            $new_group_list = [];
            if(!empty($group_list)){
                foreach ($group_list as $k => $v){
                    $new_group_list[$v['id']] = $v['title'];
                }
            }
            $this->form_list = [
                ['file' => 'title','title' => '表单名称','type' => 'text',],
                ['file' => 'cont','title' => '表单介绍','type' => 'editor',],
                ['file' => 'see_group','title' => '允许查看','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => ''],],
                ['file' => 'add_group','title' => '允许发布','type' => 'checkbox','data' => ['list' => $new_group_list,'default' => ''],],
                ['file' => 'tourist','title' => '游客','type' => 'radio','data' => ['list' => ['1' => '允许发布','0' => '禁止发布'],'default' => '0']],
                ['file' => 'status','title' => '是否启用','type' => 'radio','data' => ['list' => ['1' => '启用','0' => '禁用'],'default' => '1']],
                ['file' => 'sort','title' => '排序','type' => 'text','default' => '0',]
            ];
            if($this->request->action() == 'edit'){
                array_push($this->form_list,['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',],
                    ['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]);
            }
        }elseif ($this->request->action() == 'trash'){
            $this->list_base['add'] = false;
            $this->list_rightbtn = [
                ['type' => 'trash','title' => '还原'],
                ['type' => 'del'],
            ];
            $this->list_top = [
                ['uri' => url('index')->build(),'title' => '模型列表','class' => 'cx-button cx-bg-blue'],
            ];
        }
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
            validate($this->validate)->scene($this->request->isPut() ? "formedit" : "formadd")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $data = $this->model->getEditAdd($data);
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if($add = $this->model->setOne($data)){
            $this->success("{$res_title}成功");
        }
        $this->error("{$res_title}失败");
    }
}