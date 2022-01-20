<?php
declare (strict_types = 1);
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\ConfigUp;
use app\common\model\form\FormModel;
use app\common\model\ModelList;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use worm\NodeFormat;

class Index extends AdminBase
{
    use AddEditList;
    protected $list_temp = 'index';
    public function index(){
        //  获取后台导航
        $menuList = $this->WormAuth();
        $menuList = NodeFormat::toLayer(Common::del_file($menuList['1']['c'],'menusee','1'));
        $menuList = $this->Menu($menuList);
        return $this->viewAdminList($menuList);
    }
    public function map(){
        $this->list_temp = 'map';
        //  查询模块
        $_model = new ModelList();
        $_modellist = $_model->getList(['class' => '0','status' => 'a'],true);
        //  查询数据统计
        if(!empty($_modellist)){
            foreach ($_modellist as $k => $v){
                $_vm = "app\\common\\model\\{$v['keys']}\\Artmodel";
                $_vm = new $_vm;
                $_modellist[$k]['data'] = $_vm->getList();
            }
        }
        $_model = new FormModel();
        $_formlist = $_model->getList(['class' => '0','status' => 'a']);
        //  查询版本号
        $_model = new ConfigUp();
        $edition = $_model->getOne();
        $listdb = [
          'modellist' => $_modellist,
          'formlist' => $_formlist,
          'edition' => $edition
        ];
        return $this->viewAdminList($listdb);
    }
    protected function Menu($data,$level = 1){
        $_data = '';
        $_n = $level * 15;
        foreach ($data as $k => $v){
            $_v = '';
            $icon = empty($v['icon']) ? '' : "<i class='{$v['icon']} cx-text-f16 cx-mag-r10'></i>";
            if(!empty($v['node'])){
                $new_v = $this->Menu($v['node'],$level + 1);
                $_v .= "<dt class='cx-fex cx-click cx-pad-l{$_n}' data-type='foldtab' data-cid='{$v['id']}'><h3 class='cx-text-f14 map-left-h3'>{$icon}{$v['title']}</h3><i class='map-left-icon cx-icon cx-text-f16 cx-iconunfold'></i></dt><dd class='map-left-dd map-left-dd{$v['id']}'>{$new_v}</dd>";
            }else{
                $fuconf = empty($v['condition']) ? [] : explode(',',$v['condition']);
                if(!empty($fuconf)){
                    $_fuconf = [];
                    foreach ($fuconf as $k1 => $v1){
                        $v1 = explode('=',$v1);
                        $_fuconf[$v1['0']] = $v1['1'];
                    }
                    $fuconf = $_fuconf;
                }
                $uri = url($v['name'],$fuconf)->build();
                $_v .= "<dd class='map-left-ddu cx-click cx-pad-l{$_n}' data-type='rightbody' data-uri='{$uri}'><h6 class='map-left-h6'>{$icon}{$v['title']}</h6></dd>";
            }
            $_data .= "<dl class='layout cx-fex-column map-left-dl'>{$_v}</dl>";
        }
        return $_data;
    }
    public function delcache(){
        Common::del_dir(root_path("{$this->webdb['web_updir']}/linshi"),false);
        Common::del_dir(root_path('runtime'),false);
        cache('userAuth_'.$this->wormuser['uid'],NULL);
        $this->success("删除缓存成功");
    }
}