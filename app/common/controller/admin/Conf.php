<?php
declare(strict_types = 1);
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\model\UserGroup;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;

abstract class Conf extends AdminBase {
    use AddEditList;
    protected $basename;
    protected $conflist;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\".$this->basename."\\".ucfirst($array['0']['4']);
        $this->model = new $mdodel;
        $this->getConf();
    }
    protected function getConf(){
        $this->list_base['title'] = $this->basename.'模块配置';
        $this->list_base['add'] = false;
        $this->list_base['uri'] = url('config/confeditsave',['class' => $this->basename])->build();
        $usermodel = new UserGroup();
        $group_list = $usermodel->getList([]);
        $new_group_list = [];
        if(!empty($group_list)){
            foreach ($group_list as $k => $v){
                $new_group_list[$v['id']] = $v['title'];
            }
        }
        $this->getdata['class'] = $this->basename;
        $this->conflist = $this->model->getConfedit($this->getdata);
        if ($this->conflist === false) {
            $this->error("还没有配置项，请先添加");
        }
        foreach ($this->conflist as $k => $v) {
            $this->form_list[] = Common::ReadFile($v);
        }
    }
    public function index(){
        return $this->viewAdminAdd($this->webdb);
    }
}