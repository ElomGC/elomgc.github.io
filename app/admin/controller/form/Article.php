<?php
declare(strict_types = 1);

namespace app\admin\controller\form;

use app\common\controller\AdminBase;
use app\common\model\Chinacode;
use app\common\model\Order;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use think\facade\Db;
use worm\NodeFormat;

class Article extends AdminBase {
    use AddEditList;
    protected $basename;
    protected $fileModel;
    protected $baseModel;
    protected $baseModelList;
    protected $validatename;
    protected $list_temp = false;
    protected function initialize()
    {
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\" . $this->basename . "\\Form";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\\FormModel";
        $this->baseModel = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\Artmodelfile";
        $this->fileModel = new $mdodel;
        $this->validate = "app\\common\\validate\\ArtArticle";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->getConf();
    }
    protected function getConf()
    {
        //  获取模型信息
        $this->baseModelList = $this->baseModel->getList(['status' => '1']);
        if (empty($this->baseModelList)) {
            $this->error("请先创建模型", url("{$this->basename}.artmodel/index")->build());
        }
        if($this->request->action() == 'index') {
            if($this->contauth['del']){
                $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
            }
            array_push($this->list_file,['file' => 'id', 'title' => 'ID', 'type' => 'text','width' => '80'],
                ['file' => 'title', 'title' => '标题', 'type' => 'text','width' => '40%'],
                ['file' => 'u_name', 'title' => '发布人', 'type' => 'text', 'class' => 'cx-text-center', 'width' => '150'],
                ['file' => 'addtime', 'title' => '时间', 'type' => 'text', 'type_edit' => 'date', 'class' => 'cx-text-center', 'width' => '110'],
                ['file' => 'status','title' => '状态','type' => 'switch','text' => '审核|待审','uri' => url('fastswitch',['mid' => $this->getdata['mid']])->build(),'textalign' => 'center','width' => '100','default' => '1']);
            $this->list_base['uri'] = url('getlist',['mid' => empty($this->getdata['mid']) ? $this->baseModelList['0']['id'] : $this->getdata['mid']])->build();
            $this->list_top = [['title' => '表单分类列表', 'class' => 'cx-button-s cx-bg-yellow cx-mag-r10','uri' => url($this->basename.'.artmodel/index')->build()]];
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加表单', 'class' => 'cx-button-s cx-bg-green','uri' => url('create',['mid' => $this->getdata['mid']])->build()];
            }
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑参数分类",'uri' => url('edit',['mid' => '__mid__','id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','width' => '80','uri' => url('del',['mid' => $this->getdata['mid'],'id' => '__id__'])->build(),];
                array_push($this->list_top,['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel',['mid' => $this->getdata['mid']])->build()]);
            }
            $this->list_nav = [
                'list' => [
                    ['id' => 'a', 'title' => '全部', 'uri' => url('index', ['status' => 'a', 'mid' => $this->getdata['mid']])->build()],
                    ['id' => '0', 'title' => '待审核', 'uri' => url('index', ['status' => '0', 'mid' => $this->getdata['mid']])->build()],
                    ['id' => '1', 'title' => '已通过', 'uri' => url('index', ['status' => '1', 'mid' => $this->getdata['mid']])->build()],
                    ['id' => '2', 'title' => '已拒绝', 'uri' => url('index', ['status' => '2', 'mid' => $this->getdata['mid']])->build()],
                ],
                'default' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
            ];
        }elseif (in_array($this->request->action(),['edit','create','save'])) {
            $this->list_base['uri'] = url('save', $this->request->action() == 'create' ? ['mid' => $this->getdata['mid']] : ['mid' => $this->getdata['mid'],'id' => $this->getdata['id']]);
            $this->list_base['title'] = $this->request->action() == 'create' ? "添加表单" : "编辑表单";
            $form_list = $this->fileModel->getList(['mid' => $this->getdata['mid']]);
            $this->form_list[] = ['file' => 'u_name','title' => '用户名','type' => 'text','disabled' => true,'default' => $this->wormuser['u_name']];
            foreach ($form_list as $k => $v) {
                array_push($this->form_list,Common::ReadFile($v));
            }
            array_push($this->form_list,['file' => 'status','title' => '状态','type' => 'radio','data' => ['list' => ['0' => '待审核','1' => '通过','2' => '拒绝'],'default' => '0']],
                ['file' => 'jian','title' => '推荐','type' => 'radio','data' => ['list' => ['0' => '不推荐','1' => '推荐'],'default' => '0']],
                ['file' => 'res_content','title' => '回复','type' => 'editor','height' => '200px'],
                ['file' => 'uid','title' => '用户UID','type' => 'text','type_edit' => 'hidden','default' => $this->wormuser['uid']],
                ['file' => 'mid','title' => '用户UID','type' => 'text','type_edit' => 'hidden','default' => $this->getdata['mid']]);
            if(in_array($this->request->action(),['edit','save'])){
                array_push($this->form_list, ['file' => 'id','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'required_list' => 'number',]);
                if($this->request->action() == 'edit'){
                    array_push($this->form_list,['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]);
                }
            }
        }
    }
    public function edit()
    {
        $data = $this->model->getOne($this->getdata);
        return $this->viewAdminAdd($data);
    }
    public function del(){
        $data = Common::data_trim(input('post.'));
        if(!$this->request->isDelete() || empty($data['id'])){
            $this->error("非法访问");
        }
        $data['mid'] = $this->getdata['mid'];
        if($this->model->DeleteOne($data)){
            $this->success("删除成功");
        }
        $this->error("删除失败");
    }
    public function pdel(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['ids'])){
            $this->error("非法访问");
        }
        $data = [
            'mid' => $this->getdata['mid'],
            'id' => $data['ids'],
        ];
        if($this->model->DeleteOne($data)){
            $this->success("删除成功");
        }
        $this->error("删除失败");
    }
    public function save()
    {
        if (!$this->request->isPost() && !$this->request->isPut()) {
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if ($this->request->isPut() && empty($data['id'])) {
            $this->error("非法访问");
        }
        try {
            validate($this->validate)->scene($this->request->isPut() ? "formedit" : "formadd")->check($data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        $resdata = Common::SetReadFile($this->form_list, $data);
        if ($resdata['code'] == '0') {
            $this->error($resdata['msg']);
        }
        $data = $this->model->getEditAdd($resdata['data']);
        //  检测是否启用订单
        $res_title = !empty($this->getdata['id']) ? "编辑" : "添加";
        if ($add = $this->model->setOne($data)) {
            $this->success("{$res_title}成功", url("{$this->basename}.article/index")->build());
        }
        $this->error("{$res_title}失败");
    }
    //  快速启用/禁用
    public function fastswitch()
    {
        if (!$this->request->isPut()) {
            $this->error("非法访问");
        }
        $pk = $this->model->getPk();
        $data = Common::data_trim(input('post.'));
        if (empty($data[$pk]) || empty($data['field'])) {
            $this->error("非法访问");
        }
        $add = [
            $pk => $data['id'],
            'mid' => $this->getdata['mid'],
            $data['field'] => $data['value'],
        ];
        if($this->model->setOne($add)){
            $this->success("处理成功");
        }
        $this->error("处理失败");
    }
}