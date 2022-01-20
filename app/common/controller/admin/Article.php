<?php
declare(strict_types = 1);
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\model\Chinacode;
use app\common\model\Order;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\exception\ValidateException;
use worm\NodeFormat;

abstract class Article extends AdminBase
{

    use AddEditList;

    protected $basename;
    protected $fileModel;
    protected $baseModel;
    protected $baseModelList;
    protected $partModel;
    protected $partModelList;
    protected $fupartModel;
    protected $fupartModelList;
    protected $FuPartArticleModel;
    protected $SpecialModel;
    protected $SpecialModelList;
    protected $SpecialArticleModel;
    protected $list_temp = false;
    protected function initialize()
    {
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\" . $this->basename . "\\" . ucfirst($array['0']['4']);
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\Artmodelfile";
        $this->fileModel = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\Artmodel";
        $this->baseModel = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\Part";
        $this->partModel = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\FuPart";
        $this->fupartModel = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\FuPartArticle";
        $this->FuPartArticleModel = new $mdodel;
        $mdodel = "app\\common\\model\\Special";
        $this->SpecialModel = new $mdodel;
        $mdodel = "app\\common\\model\\SpecialArticle";
        $this->SpecialArticleModel = new $mdodel;
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
        //  获取栏目信息
        $mid = array_column($this->baseModelList, 'id');
        $this->partModelList = NodeFormat::toList($this->partModel->getList($this->request->action() == 'trash' ? ['getdeltime' => '1001', 'status' => 'a', 'class' => '0'] : ['mid' => $mid,'group_edit' => $this->wormuser['u_groupid'] == '1' ? '' : $this->wormuser['u_groupid'],'group_uid' => $this->wormuser['u_groupid'] == '1' ? '' : $this->wormuser['uid'], 'class' => '0']));
        if (empty($this->partModelList)) {
            $this->error("请先添加栏目", url("{$this->basename}.part/index")->build());
        }
        //  获取专题信息
        $this->SpecialModelList = $this->SpecialModel->getList();
        $this->fupartModelList = $this->fupartModel->getList();
        $this->fupartModelList = empty($this->fupartModelList) ? [] : NodeFormat::toList($this->fupartModelList);
        if(!empty($this->fupartModelList)){
            foreach ($this->fupartModelList as $k => $v){
                $this->fupartModelList[$k]['title'] = $v['title_display'];
            }
        }
        //  如果栏目在回收站，就去除发表，并且只展示当前栏目
        $getfu = [];
        if (!empty($this->getdata['fid'])) {
            $getfu['fid'] = $this->getdata['fid'];
            $_part = Common::del_file($this->partModelList, 'id', $this->getdata['fid']);
            if (empty($_part)) {
                $this->partModelList = $this->partModel->getList(['id' => $this->getdata['fid'], 'getdeltime' => '1001', 'class' => '0', 'status' => 'a']);
            }
        }
        if (!empty($this->getdata['mid'])) {
            $getfu['mid'] = $this->getdata['mid'];
            $_model = Common::del_file($this->baseModelList, 'id', $this->getdata['mid']);
            if (empty($_model)) {
                $this->baseModelList = $this->baseModel->getList(['id' => $this->getdata['mid'], 'getdeltime' => '1001', 'status' => 'a']);
                $this->partModelList = $this->partModel->getList(['mid' => $this->getdata['mid'], 'getdeltime' => '1001', 'status' => 'a']);
                $this->list_rightbtn = [];
            }
        }
        if(isset($this->getdata['status'])){
            $getfu['status'] = $this->getdata['status'];
        }
        $_partlist = [];
        $_partlist_ = [['title' => '全部', 'value' => '']];
        foreach ($this->partModelList as $k => $v) {
            $_partlist[$v['id']] = $v['title'];
            if(!empty($v['title_display']) && $v['class'] == '0') {
                $_partlist_[] = ['title' => $v['title_display'], 'value' => $v['id']];
            }
        }
        if ($this->contauth['del']) {
            $this->list_file = [['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40']];
        }
        array_push($this->list_file,['file' => 'id','title' => 'ID','type' => 'text','textalign' => 'center','fixed' => 'left','width' => '80'],
            ['file' => 'title', 'title' => '标题', 'type' => 'link','target' => '_blank','uri' => url('/home/'.$this->basename.'/article/index',['id'=>'__id__'])->build(),'width' => '40%'],
            ['file' => 'part_name', 'title' => '栏目', 'type' => 'text', 'class' => 'cx-text-center', 'width' => '180'],
            ['file' => 'u_name', 'title' => '发布人', 'type' => 'text','textalign' => 'center', 'width' => '150'],
            ['file' => 'addtime', 'title' => '发表时间', 'type' => 'text', 'textalign' => 'center','width' => '180'],
            ['file' => 'sort', 'title' => '排序', 'type' => 'edit', 'textalign' => 'center','width' => '150',],
            ['file' => 'status','title' => '状态','type' => 'switch','text' => '审核|待审','textalign' => 'center','width' => '100','default' => '1']
        );
        if($this->request->action() == 'index') {
            $this->list_base['uri'] = url('getlist',$getfu)->build();
            if ($this->contauth['create']) {
                $this->list_base['add'] = ['title' => '添加内容','class' => 'cx-button-s cx-bg-green', 'uri' => url('create',$getfu)->build() . "?" . http_build_query($getfu),'full' => 'y',];
            }
            if ($this->contauth['edit']) {
                $this->list_file[] = ['filed' => 'edit', 'title' => '编辑', 'type' => 'btn', 'text' => true, 'open' => true, 'opentitle' => "编辑内容", 'uri' => url('edit', ['id' => '__id__'])->build(), 'icon' => 'cx-iconbianji3 cx-text-f16', 'class' => 'cx-text-green', 'textalign' => 'center', 'width' => '60', 'fixed' => 'right','full' => 'y'];
            }
            if ($this->contauth['del']) {
                $this->list_file[] = ['filed' => 'del', 'title' => '删除', 'type' => 'btn', 'event' => 'del', 'text' => true, 'icon' => 'cx-iconlajixiang cx-text-f16', 'class' => 'cx-text-red', 'textalign' => 'center', 'width' => '60', 'fixed' => 'right',];
                $this->list_top = [
                    ['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                    ['title' => '回收站', 'class' => 'cx-button-s cx-bg-yellow cx-mag-r10', 'uri' => url('trash', $getfu)->build()],
                ];
            }
            $this->list_nav = [
                'list' => [
                    ['id' => 'a', 'title' => '全部', 'uri' => url('index', ['status' => 'a', 'fid' => empty($this->getdata['fid']) ? '0' : $this->getdata['fid']])->build()],
                    ['id' => '0', 'title' => '待审核', 'uri' => url('index', ['status' => '0', 'fid' => empty($this->getdata['fid']) ? '0' : $this->getdata['fid']])->build()],
                    ['id' => '1', 'title' => '已通过', 'uri' => url('index', ['status' => '1', 'fid' => empty($this->getdata['fid']) ? '0' : $this->getdata['fid']])->build()],
                    ['id' => '2', 'title' => '已拒绝', 'uri' => url('index', ['status' => '2', 'fid' => empty($this->getdata['fid']) ? '0' : $this->getdata['fid']])->build()],
                ],
                'default' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
            ];
            $this->list_search = [
                'fieldname' => 'fid',
                'field' => $_partlist_,
                'input' => ['name' => 'key'],
                'uri' => url('getlist')->build(),
            ];
        }else if (in_array($this->request->action(),['edit','create'])) {
            if ($this->request->action() == 'create') {
                if (empty($this->getdata['fid']) && empty($this->getdata['mid'])) {
                    $this->redirect(url($this->basename . '.article/partend'));
                }
            }
            $map['mid'] = empty($this->getdata['mid']) ? '0' : $this->getdata['mid'];
            $map['fid'] = empty($this->getdata['fid']) ? '0' : $this->getdata['fid'];
            $map['id'] = empty($this->getdata['id']) ? '0' : $this->getdata['id'];
            $this->redirect(url($this->basename . '.article/addnew', $map)->build());
        } else if ($this->request->action() == 'trash') {
            $this->list_base['uri'] = url('getlist',['del_time' => '1001','status' => 'a'])->build();
            $this->list_top = [
                ['title' => '内容列表', 'class' => 'cx-button-s cx-bg-blue cx-mag-r10', 'uri' => url('index')->build()],
            ];
            if ($this->contauth['edit']) {
                $this->list_file[] = ['filed' => 'trash', 'title' => '还原', 'type' => 'btn', 'text' => true, 'event' => 'trash', 'opentitle' => "编辑栏目", 'uri' => url('trashone', ['id' => '__id__'])->build(), 'icon' => 'cx-iconhuanyuan1 cx-text-f16', 'class' => 'cx-text-green', 'textalign' => 'center', 'width' => '80', 'fixed' => 'right','full' => 'y'];
            }
            if ($this->contauth['del']) {
                $this->list_file[] = ['filed' => 'del', 'title' => '删除', 'type' => 'btn', 'event' => 'del', 'text' => true, 'icon' => 'cx-iconlajixiang cx-text-f16', 'class' => 'cx-text-red', 'textalign' => 'center', 'width' => '80', 'fixed' => 'right',];
                array_push($this->list_top,['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()], ['title' => '回收站', 'class' => 'cx-button-s cx-bg-yellow cx-mag-r10', 'uri' => url('trash', $getfu)->build()]);
            }
            $this->list_base['add'] = false;
        }
    }
    public function partend()
    {
        $this->list_base['uri'] = url($this->basename.'.part/getlist',empty($this->getdata['mid']) ? ['class' => '0'] : ['mid' => $this->getdata['mid'],'class' => '0'])->build();
        $this->list_base['page'] = '1';
        $this->list_base['open'] = 'false';
        $this->list_base['tool'] = '0';
        $this->list_base['toolbar'] = '0';
        $this->list_file = [
            ['file' => 'id', 'title' => 'FID','type' => 'text','textalign' => 'center','width' => '80'],
            ['file' => 'title', 'title' => '栏目', 'type' => 'link', 'uri' => url($this->basename . '.article/create', ['fid' => '__id__'])->build(),'width' => '80%'],
            ['filed' => 'edit','title' => '发表','type' => 'btn','text' => true,'open_edit' => 'lay','uri' => url($this->basename . '.article/create', ['fid' => '__id__'])->build(),'icon' => 'cx-iconedit cx-text-f16','class' => 'cx-text-green','textalign' => 'center','width' => '80'],
        ];
        return $this->viewAdminList();
    }
    //  获取请求参数
    protected function getMap()
    {
        $mid = empty($this->getdata['mid']) ? array_column($this->baseModelList, 'id') : $this->getdata['mid'];
        $fid = empty($this->getdata['fid']) ? array_column($this->partModelList, 'id') : $this->getdata['fid'];
        if (!empty($this->getdata['fid'])) {
            $_part = Common::del_file($this->partModelList, 'id', $fid);
            $mid = $_part['0']['mid'];
        }
        $map = [
            'mid' => $mid,
            'fid' => $fid,
            'sid' => empty($this->getdata['sid']) ? '' : $this->getdata['sid'],
            'status' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
            'uid' => empty($this->getdata['uid']) ? null : $this->getdata['uid'],
            'page' => empty($this->getdata['page']) ? '' : $this->getdata['page'],
            'limit' => empty($this->getdata['limit']) ? '' : $this->getdata['limit'],
            'field' => empty($this->getdata['field']) ? '' : $this->getdata['field'],
            'key' => empty($this->getdata['key']) ? '' : $this->getdata['key'],
            'del_time' => empty($this->getdata['del_time']) ? '' : $this->getdata['del_time'],
        ];
        return $map;
    }
    //  获取模型
    protected function getBaseForm($mid)
    {
        $file_list = $this->fileModel->getList(['mid' => $mid]);
        $model = Common::del_file($this->baseModelList, 'id', $mid);
        $model = $model['0'];
        //  获取栏目列表
        $partlist = Common::del_file($this->partModelList, 'mid', $mid);
        if (empty($partlist)) {
            $this->error("请创建栏目");
        }
        $_partlist = [];
        foreach ($partlist as $k => $v) {
            $_partlist[$v['id']] = $v['title_display'];
        }
        $res = [
            ['file' => 'fid', 'title' => '请选择栏目', 'type' => 'select', 'data' => ['list' => $_partlist, 'default' => ''], 'required' => true, 'required_list' => 'number'],
            ['file' => 'mid', 'title' => '审核', 'type' => 'text', 'type_edit' => 'hidden', 'required' => true, 'default' => $mid, 'required_list' => 'number',],
            ['file' => 'id', 'title' => '审核', 'type' => 'text', 'type_edit' => 'hidden'],
            ['file' => 'uid', 'title' => '审核', 'type' => 'text', 'type_edit' => 'hidden']
        ];
        foreach ($file_list as $k => $v) {
            array_push($res,Common::ReadFile($v));
        }

        $res[] = ['file' => 'jumpurl', 'title' => '跳转链接', 'type' => 'text','type_group' => '更多选项', 'tip' => '请填写跳转链接的完整网址'];
        //  添加辅栏目选择
        if (!empty($this->fupartModelList)) {
            array_push($res,['file' => 'fuid', 'title' => '请选择辅栏目', 'type_group' => '更多选项', 'type' => 'checkboxlist', 'data' => ['data' => $this->fupartModelList,'total' => '1','per_page' => '1']]);
        }
        //  添加专题选择
        if ($this->SpecialModelList['total'] > '0') {
            array_push($res,['file' => 'sid', 'title' => '请选择专题', 'type_group' => '更多选项', 'type' => 'checkboxlist', 'data' => $this->SpecialModelList,'uri' => url('Special/index',['status' => '1'])->build()]);
        }
        if ($model['order'] == '1') {
            array_push($res,['file' => 'stock_type', 'title' => '库存类型', 'type' => 'radio', 'type_group' => '订单结算', 'data' => ['list' => ['0' => '不限库存','1' => '指定库存'], 'default' => '1'],'required' => true,'file_link' => ['1' => ['stock']]],
                ['file' => 'stock', 'title' => '库存数量', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'required' => true,'required_list' => 'number',],
                ['file' => 'money', 'title' => '市场价格', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true],
                ['file' => 'money_zk', 'title' => '市场折扣价格', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'tip' => '为0时不打折', 'type_unit' => '元', 'required' => true]
            );
            //  检测是否启用三级分销
            if(!empty($this->webdb[$this->basename.'_sale_three'])){
                array_push($res,['file' => 'sale_one', 'title' => '一级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                if($this->webdb[$this->basename.'_sale_three'] >= '2'){
                    array_push($res,['file' => 'sale_two', 'title' => '二级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                }
                if($this->webdb[$this->basename.'_sale_three'] == '3'){
                    array_push($res,['file' => 'sale_three', 'title' => '三级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                }
            }
            //  检测是否存在FID
            if(!empty($this->getdata['fid'])){
                $_part = Common::del_file($partlist,'id',$this->getdata['fid']);
            }
            //  检测是否启用多规格
            if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                $_filelist = [[
                    ['file' => 'parameter', 'title' => '规格', 'type' => 'text', 'required' => true],
                    ['file' => 'money', 'title' => '价格', 'type' => 'text', 'default' => '0', 'type_unit' => '元', 'required' => true],
                    ['file' => 'money_zk', 'title' => '折扣价格', 'type' => 'text', 'default' => '0', 'type_unit' => '元', 'required' => true],
                    ['file' => 'stock', 'title' => '库存', 'type' => 'text', 'default' => '0', 'required' => true, 'required_list' => 'number'],
                ]];
                if(!empty($this->webdb[$this->basename.'_sale_three'])){
                    array_push($_filelist['0'],['file' => 'sale_one', 'title' => '一级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                    if($this->webdb[$this->basename.'_sale_three'] >= '2'){
                        array_push($_filelist['0'],['file' => 'sale_two', 'title' => '二级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                    }
                    if($this->webdb[$this->basename.'_sale_three'] == '3'){
                        array_push($_filelist['0'],['file' => 'sale_three', 'title' => '三级分销返利', 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','tip' => '为0时使用统一返利比例', 'type_unit' => '元', 'required' => true]);
                    }
                }
                array_push($_filelist['0'],['file' => "sort", 'title' => "排序", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'required' => true]);
                $res[] = ['file' => 'parametermoney', 'title' => '自订义参数', 'type' => 'fielgroup', 'class' => 'cx-button cx-bor-blue', 'button' => '增加规格', 'type_group' => '订单结算', 'data' => $_filelist];
            }
            if ($model['order_group'] == '1' || $model['order_group'] == '2') {
                $usermodel = new \app\common\model\User();
                $usergroup = $usermodel->getAuthGroup();
                $usergroup = Common::del_file($usergroup, 'group_type', '1');
                $usergroup = array_merge([], $usergroup);
                $moneyText = $model['order_group'] == '1' ? "价格" : "返利";
                $_filelist = [];
                foreach ($usergroup as $k => $v) {
                    $res[] = ['file' => "groupid_{$v['id']}", 'title' => "{$v['title']}{$moneyText}", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                    if(!empty($_part['0']['order_level']) && $_part['0']['order_level'] == '1') {
                        $_filelist['0'][] = ['file' => "groupid_{$v['id']}", 'title' => "{$v['title']}{$moneyText}", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                    }
                }
                //  检测是否启用多规格
                if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                    foreach ($res as $k => $v) {
                        if($v['file'] == 'parametermoney'){
                            $v['data']['0'] = array_merge($v['data']['0'],$_filelist['0']);
                            $res[$k] = $v;
                        }
                        continue;
                    }
                }
            }else if($model['order_group'] == '4' && !empty($this->getdata['fid'])){
                //  查询投放城市
                $_chinalist = empty($_part['0']['chinalist']) ? [] : explode(',',$_part['0']['chinalist']);
                $_chinalist = empty($_chinalist) ? [] : Common::del_null($_chinalist);
                if(!empty($_chinalist)){
                    $chinaModel = new Chinacode();
                    $_chinalist = $chinaModel->getList(['zoneid' => $_chinalist]);
                    $_chinalist = NodeFormat::config(['id' => 'zoneid','pid' => 'parzoneid','title' => 'zonename'])->toList($_chinalist);
                    $_filelist = [];
                    foreach ($_chinalist as $k => $v) {
                        $res[] = ['file' => "chinacode_{$v['zoneid']}", 'title' => "{$v['zonename']}市场价格", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                        $res[] = ['file' => "chinacode_{$v['zoneid']}money_zk", 'title' => "{$v['zonename']}折扣价格", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                        if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                            $_filelist['0'][] = ['file' => "chinacode_{$v['zoneid']}", 'title' => "{$v['zonename']}市场价格", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                            $_filelist['0'][] = ['file' => "chinacode_{$v['zoneid']}money_zk", 'title' => "{$v['zonename']}折扣价格", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                        }
                        //  检测是否启用三级分销
                        if(!empty($this->webdb[$this->basename.'_sale_three'])){
                            $res[] = ['file' => "chinacode_{$v['zoneid']}sale_one", 'title' => "{$v['zonename']}一级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                            if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                                $_filelist['0'][] = ['file' => "chinacode_{$v['zoneid']}sale_one", 'title' => "{$v['zonename']}一级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0', 'type_unit' => '元', 'required' => true];
                            }
                            if($this->webdb[$this->basename.'_sale_three'] >= '2'){
                                $res[] = ['file' => "chinacode_{$v['zoneid']}sale_two", 'title' => "{$v['zonename']}二级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','type_unit' => '元', 'required' => true];
                                if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                                    $_filelist['0'][] = ['file' => "chinacode_{$v['zoneid']}sale_two", 'title' => "{$v['zonename']}二级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','type_unit' => '元', 'required' => true];
                                }
                            }
                            if($this->webdb[$this->basename.'_sale_three'] == '3'){
                                $res[] = ['file' => "chinacode_{$v['zoneid']}sale_three", 'title' => "{$v['zonename']}三级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','type_unit' => '元', 'required' => true];
                                if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                                    $_filelist['0'][] = ['file' => "chinacode_{$v['zoneid']}sale_three", 'title' => "{$v['zonename']}三级返利", 'type' => 'text', 'type_group' => '订单结算', 'default' => '0','type_unit' => '元', 'required' => true];
                                }
                            }
                        }
                    }
                    //  检测是否启用多规格
                    if(!empty($_part['0']['order_level'] == '1') && $_part['0']['order_level'] == '1') {
                        foreach ($res as $k => $v) {
                            if($v['file'] == 'parametermoney'){
                                $v['data']['0'] = array_merge($v['data']['0'],$_filelist['0']);
                                $res[$k] = $v;
                            }
                            continue;
                        }
                    }
                }
            }
        }
        $see_list = ['0' => "草稿", '1' => '审核'];
        if (!empty($model['see_add'])) {
            $see_list['2'] = "拒绝";
        }
        $res[] = ['file' => 'status', 'title' => '审核', 'type' => 'radio', 'data' => ['list' => $see_list, 'default' => '1'],];
        $res[] = ['file' => 'jian', 'title' => '推荐', 'type' => 'select', 'data' => ['list' => ['9' => '固定置顶', '1' => '1级推荐', '2' => '2级推荐', '3' => '3级推荐', '4' => '4级推荐', '5' => '5级推荐', '6' => '6级推荐'], 'default' => '']];
        if (!empty($model['see_picurl'])) {
            $res[] = ['file' => 'picurl', 'title' => '封面图', 'type' => 'upload', 'upload_filenum' => '1', 'upload_autoup' => '1'];
        }
        if (!empty($model['see_keyword'])) {
            $res[] = ['file' => 'keywords', 'title' => '标签', 'type' => 'text', 'tip' => '多个标签请用英文“,”进行分割'];
        }
        if (!empty($model['see_description'])) {
            $res[] = ['file' => 'description', 'title' => '简介', 'type' => 'textarea'];
        }
        return $res;
    }

    protected function getRead($id)
    {
        $data = $this->model->whereId($id)->find()->toArray();
        $data = $this->model->getOne($data['mid'], $data['id']);
        return $data;
    }

    public function addnew()
    {
        $this->list_base['uri'] = url('save')->build();
        $this->getdata['mid'] = empty($this->getdata['mid']) ? null : $this->getdata['mid'];
        if (!empty($this->getdata['id'])) {
            $this->getdata = $this->getRead($this->getdata['id']);
        } else if (empty($this->getdata['mid']) && !empty($this->getdata['fid'])) {
            $part = Common::del_file($this->partModelList, 'id', $this->getdata['fid']);
            $this->getdata['mid'] = $part['0']['mid'];
        }
        $this->form_list = $this->getBaseForm($this->getdata['mid']);
        return $this->viewAdminAdd($this->getdata);
    }
    /**
     * 快速编辑
     */
    public function fastedit(){
        if(!$this->request->isPost()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['id']) || empty($data['filed'])){
            $this->error("非法访问");
        }
        $add['id'] = $data['id'];
        $add[$data['filed']] = $data['value'];
        try {
            validate($this->validate)->scene("fastedit")->check($add);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $old = $this->model->whereId($add['id'])->find();
        $old[$data['filed']] = $data['value'];
        $old = is_array($old) ? $old : $old->toArray();
        if ($add = $this->model->setOne($old)) {
            $this->success('处理成功');
        }
        $this->error("处理失败");
    }
    //  保存数据
    public function save()
    {
        if (!$this->request->isPost() && !$this->request->isPut()) {
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if ($this->request->isPut() && empty($data['id'])) {
            $this->error("非法访问");
        }
        $data['uid'] = empty($data['uid']) ? $this->wormuser['uid'] : $data['uid'];
        try {
            validate($this->validate)->scene($this->request->isPut() ? "edit" : "add")->check($data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        $this->getdata['fid'] = $data['fid'];
        $form_list = $this->getBaseForm($data['mid']);
        $_data = empty($data['id']) ? [] : $this->model->getOne($data['mid'], $data['id']);
        $resdata = Common::SetReadFile($form_list, $data, $_data);
        if ($resdata['code'] == '0') {
            $this->error($resdata['msg']);
        }
        $_data = array_merge($_data, $resdata['data']);
        $_sid = empty($_data['sid']) ? '' : $_data['sid'];
        $_fuid = empty($_data['fuid']) ? '' : $_data['fuid'];
        $_data = $this->model->getEditAdd($_data);
        //  检测是否启用订单
        $res_title = !empty($this->getdata['id']) ? "编辑" : "添加";
        if ($add = $this->model->setOne($_data)) {
            //  检测是否存在专题
            if(!empty($_sid)){
                $sid = Common::del_null(explode(',',$_sid));
                $_sid = [];
                foreach ($sid as $k => $v){
                    $_sid[] = [
                        'sid' => $v,
                        'model' => $this->basename,
                        'aid' => $add['id'],
                        'status' => $add['status'],
                    ];
                }
                $this->SpecialArticleModel->setOne($_sid);
            }else{
                $this->SpecialArticleModel->whereModel($this->basename)->whereAid($add['id'])->delete();
            }
            //  检测是否存在辅助栏目
            if(!empty($_fuid)){
                $sid = Common::del_null(explode(',',$_fuid));
                $_sid = [];
                foreach ($sid as $k => $v){
                    if(empty($v)){
                        continue;
                    }
                    $_sid[] = [
                        'fuid' => $v,
                        'aid' => $add['id'],
                        'status' => $add['status'],
                    ];
                }
                $this->FuPartArticleModel->setOne($_sid);
            }else{
                $this->FuPartArticleModel->whereAid($add['id'])->delete();
            }
            //  生成订单信息
            //  准备放弃，改用插件
            $_model = Common::del_file($this->baseModelList, 'id', $add['mid']);
            $_model = $_model['0'];
            if($_model['order'] == '1'){
                //  生成价格信息
                $base = [
                    'parameter' => '0',
                    'stock_type' => $data['stock_type'],
                    'stock' => empty($data['stock_type']) ? '0' : $data['stock'],
                    'money_type' => '0',
                    'mid' => $add['mid'],
                    'aid' => $add['id'],
                    'groupid' => '0',
                    'chinacode' => '0',
                    'money' => $data['money'] * 100,
                    'money_zk' => $data['money_zk'] * 100,
                    'sale_one' => '0',
                    'sale_two' => '0',
                    'sale_three' => '0',
                ];
                //  检测是否启用三级分销
                if(!empty($this->webdb[$this->basename.'_sale_three'])){
                    $base['sale_one'] = empty($data['sale_one']) ? '0' : $data['sale_one'] * 100;
                    if($this->webdb[$this->basename.'_sale_three'] >= '2'){
                        $base['sale_two'] = empty($data['sale_two']) ? '0' : $data['sale_two'] * 100;
                    }
                    if($this->webdb[$this->basename.'_sale_three'] == '3'){
                        $base['sale_three'] = empty($data['sale_three']) ? '0' : $data['sale_three'] * 100;
                    }
                }
                $order[] = $base;
                if(in_array($_model['order_group'],['1','2','4'])) {
                    $usergroup = [];
                    if(in_array($_model['order_group'],['1','2'])) {
                        $usermodel = new \app\common\model\UserGroup();
                        $usergroup = $usermodel->getList(['group_type' => '1']);
                    }else {
                        $_part = Common::del_file($this->partModelList, 'id', $add['fid']);
                        $_chinalist = empty($_part['0']['chinalist']) ? [] : explode(',', $_part['0']['chinalist']);
                        $_chinalist = empty($_chinalist) ? [] : Common::del_null($_chinalist);
                        if(!empty($_chinalist)) {
                            $chinaModel = new Chinacode();
                            $_chinalist = $chinaModel->getList(['zoneid' => $_chinalist]);
                            $usergroup = NodeFormat::config(['id' => 'zoneid', 'pid' => 'parzoneid', 'title' => 'zonename'])->toList($_chinalist);
                        }
                    }
                    if(!empty($usergroup)){
                        $_vname = $_model['order_group'] == '4' ? 'chinacode' : 'groupid';
                        foreach ($usergroup as $k => $v) {
                            $v['id'] = $_model['order_group'] == '4' ? $v['zoneid'] : $v['id'];
                            if(!isset($data[$_vname.'_'.$v['id']])){
                                continue;
                            }
                            $money = empty($data[$_vname.'_'.$v['id']]) ? '0' : $data[$_vname.'_'.$v['id']] * 100;
                            $money_zk = empty($data[$_vname.'_'.$v['id'].'money_zk']) ? '0' : $data[$_vname.'_'.$v['id'].'money_zk'] * 100;
                            $sale_one = empty($data[$_vname.'sale_one']) ? '0' : $data[$_vname.'sale_one'] * 100;
                            $sale_two = empty($data[$_vname.'sale_two']) ? '0' : $data[$_vname.'sale_two'] * 100;
                            $sale_three = empty($data[$_vname.'sale_three']) ? '0' : $data[$_vname.'sale_three'] * 100;
                            $_v = [
                                'parameter' => '0',
                                'stock_type' => '0',
                                'stock' => '0',
                                'money_type' => '0',
                                'mid' => $add['mid'],
                                'aid' => $add['id'],
                                'groupid' => $_model['order_group'] == '4' ? '0' : $v['id'],
                                'chinacode' => $_model['order_group'] == '4' ? $v['id'] : '0',
                                'money' => $money,
                                'money_zk' => $money_zk,
                                'sale_one' => $sale_one,
                                'sale_two' => $sale_two,
                                'sale_three' => $sale_three,
                            ];
                            $order[] = $_v;
                        }
                    }
                }
                //  检测是否存在自订义规格
                if(!empty($data['parametermoney'])){
                    foreach ($data['parametermoney'] as $k => $v){
                        $_v = [
                            'parameter' => $v['parameter'],
                            'stock_type' => $data['stock_type'],
                            'stock' => empty($data['stock_type']) ? '0' : $v['stock'],
                            'money_type' => '0',
                            'mid' => $add['mid'],
                            'aid' => $add['id'],
                            'groupid' => '0',
                            'chinacode' => '0',
                            'money' => $v['money'] * 100,
                            'money_zk' => $v['money_zk'] * 100,
                            'sale_one' => empty($v['sale_one']) ? '0' : $v['sale_one'] * 100,
                            'sale_two' => empty($v['sale_two']) ? '0' : $v['sale_two'] * 100,
                            'sale_three' => empty($v['sale_three']) ? '0' : $v['sale_three'] * 100,
                        ];
                        $order[] = $_v;
                        if(!empty($usergroup) && in_array($_model['order_group'],['1','2','4'])) {
                            foreach ($usergroup as $k1 => $v1) {
                                $v1['id'] = $_model['order_group'] == '4' ? $v1['zoneid'] : $v1['id'];
                                if(!isset($v[$_vname.'_'.$v1['id']])){
                                    continue;
                                }
                                $money = empty($v[$_vname.'_'.$v1['id']]) ? '0' : $v[$_vname.'_'.$v1['id']] * 100;
                                $money_zk = empty($v[$_vname.'_'.$v1['id'].'money_zk']) ? '0' : $v[$_vname.'_'.$v1['id'].'money_zk'] * 100;
                                $sale_one = empty($v[$_vname.'_'.$v1['id'].'sale_one']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_one'] * 100;
                                $sale_two = empty($v[$_vname.'_'.$v1['id'].'sale_two']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_two'] * 100;
                                $sale_three = empty($v[$_vname.'_'.$v1['id'].'sale_three']) ? '0' : $v[$_vname.'_'.$v1['id'].'sale_three'] * 100;
                                $_v = [
                                    'parameter' => $v['parameter'],
                                    'stock_type' => '0',
                                    'stock' => '0',
                                    'money_type' => '0',
                                    'mid' => $add['mid'],
                                    'aid' => $add['id'],
                                    'groupid' => $_model['order_group'] == '4' ? '0' : $v1['id'],
                                    'chinacode' => $_model['order_group'] == '4' ? $v1['id'] : '0',
                                    'money' => $money,
                                    'money_zk' => $money_zk,
                                    'sale_one' => $sale_one,
                                    'sale_two' => $sale_two,
                                    'sale_three' => $sale_three,
                                ];
                                $order[] = $_v;
                            }
                        }
                    }
                }
                $orderModel = new Order();
                $orderModel->setOne($order,$this->basename,$_model['order_group'] == '4' ? 'chinacode' : 'groupid');
            }

            $this->success("{$res_title}成功", url("{$this->basename}.article/index")->build());
        }
        $this->error("{$res_title}失败");
    }
    //  内容聚合
    public function household()
    {
        $getlist = $this->model->getHousehole($this->getMap());
        return $this->viewAdminList($getlist);
    }
}