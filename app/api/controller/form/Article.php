<?php
declare(strict_types = 1);
namespace app\api\controller\form;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;

class Article extends ApiBase {
    use AddEditList;
    protected $form_list;
    protected $baseModel;
    protected $fileModel;
    protected $validatename;
    protected function initialize()
    {
        parent::initialize();
        if(empty($this->getdata['mid'])){
            $this->result('','0','非法访问');
        }
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
        $mdodel = $this->baseModel->getOne($this->getdata['mid']);
        if(!$this->isLogin() && $mdodel['tourist'] == '0'){
            $this->result('','10000','请登录');
        }
        if (in_array($this->request->action(),['edit','create','save'])) {
            $form_list = $this->fileModel->getList(['mid' => $this->getdata['mid']]);
            foreach ($form_list as $k => $v) {
                $this->form_list[] = Common::ReadFile($v);
            }
            $this->form_list[] = ['file' => 'uid','title' => '用户UID','type' => 'text','type_edit' => 'hidden','default' => empty( $this->wormuser['uid']) ? '' :  $this->wormuser['uid']];
            $this->form_list[] = ['file' => 'mid','title' => '用户UID','type' => 'text','type_edit' => 'hidden','default' => $this->getdata['mid']];
            if(in_array($this->request->action(),['edit','save'])){
                if ($this->request->action() == 'edit' || $this->request->isPut()) {
                    array_push($this->form_list, ['file' => 'id', 'title' => 'ID', 'type' => 'text', 'type_edit' => 'hidden', 'required' => true, 'required_list' => 'number',]);
                }
                if($this->request->action() == 'edit'){
                    array_push($this->form_list,['file' => '_method','title' => 'ID','type' => 'text','type_edit' => 'hidden','required' => true,'default' => 'PUT',]);
                }
            }
        }
    }
    protected function getMap()
    {
        $map = [
            'mid' => $this->getdata['mid'],
            'id' => empty($this->getdata['id']) ? '' : $this->getdata['id'],
            'uid' => empty($this->getdata['uid']) ? '' : $this->getdata['uid'],
        ];
        return $map;
    }
    //  添加内容
    public function create(){
        return $this->viewApiRead($this->form_list);
    }
    public function read()
    {
        $data = $this->model->getOne($this->getdata);
        if($data['uid'] != $this->wormuser['uid']){
            $this->result('','0','非法访问');
        }
        $this->result($data,'1','获取成功');
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
}