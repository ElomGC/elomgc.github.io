<?php
declare(strict_types = 1);

namespace app\common\model;

use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Db;
use think\facade\Request;
use think\Model;
use worm\NodeFormat;

abstract class ArticleBase extends Model {
    protected $table;
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

    protected $schema = [
        'id'          => 'int',
        'mid'          => 'int',
        'uid'          => 'varchar',
        'fid'          => 'int',
        'status'          => 'int',
        'jian'          => 'int',
        'zan'          => 'int',
        'addtime'      => 'int',
        'del_time'      => 'int',
        'hist'      => 'int',
        'sort'      => 'int',
        'pick'      => 'int',
    ];
    /**
     * 初始化模型信息
     */
    protected $base_table;
    protected $model_key;
    protected $table_prefix;
    protected function initialize():void{
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $this->base_table = $array['0']['3']."_content";
        $this->model_key = $array['0']['3'];
        $this->table_prefix = config('database.connections.mysql.prefix');
        $this->table = $this->table_prefix.$this->model_key."_content";
    }
    //  查询索引表
    protected function getBaseList($data):array {
        $map = $this;
        $map = !empty($data['mid']) ? $map->whereIn('mid',is_array($data['mid']) ? $data['mid'] : (string) $data['mid']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['fid']) ? $map->whereIn('fid',is_array($data['fid']) ? $data['fid'] : (string) $data['fid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $map = !empty($data['picurl']) ? $map->wherePick('>','0') : $map;
        if(!empty($data['jian'])){
            $map = $data['jian'] == '10' ? $map->where('jian','>','0') : $map->whereJian($data['jian']);
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if(empty($data['getdeltime'])) {
            $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time', '>', '0');
        }
        $map = empty($data['order']) ? $map->order('jian desc,addtime desc,id desc') : $map->order($data['order']);
        $limit = [
            'list_rows' => empty($data['limit']) ? '24' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        if(!empty($data['limit_num']) && $data['limit_num'] > '1'){
            $getlist = [
                'total' => $data['limit'],
                'per_page' => $data['limit'],
                'current_page' => '1',
                'last_page' => '1',
                'page' => '',
                'data' => $map->limit((int) $data['limit_num'],(int) $data['limit'])->select()->toArray(),
            ];
        }else{
            $getlist = $map->paginate($limit);
            $page = $getlist->render();
            $getlist = Common::JobArray($getlist);
            $getlist['page'] = $page;
        }
        return $getlist;
    }
    //  查询模型详表
    public function getTableKey($data):array {
        $map = Db::name($this->base_table."_{$data['mid']}");
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = !empty($data['fid']) ? $map->whereIn('fid',is_array($data['fid']) ? $data['fid'] : (string) $data['fid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $map = !empty($data['picurl']) ? $map->whereNotNull('picurl') : $map;
        if(!empty($data['jian'])){
            $map = $data['jian'] == '10' ? $map->where('jian','>','0') : $map->whereJian($data['jian']);
        }
        if(!empty($data['filed']) && !empty($data['key'])){
            $map = $map->whereLike($data['filed'],"%{$data['key']}%");
        }else if(!empty($data['key'])){
            $map = $map->whereLike('title|keywords',"%{$data['key']}%");
        }
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        if(empty($data['getdeltime'])) {
            $map = empty($data['del_time']) ? $map->whereDelTime('0') : $map->where('del_time', '>', '0');
        }
        $map = empty($data['order']) ? $map->order('jian desc,sort desc,addtime desc,id desc') : $map->order($data['order']);
        $limit = [
            'list_rows' => empty($data['limit']) ? '24' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        if(!empty($data['limit_num']) && $data['limit_num'] > '1'){
            $getlist = [
                'total' => $data['limit'],
                'per_page' => $data['limit'],
                'current_page' => '1',
                'last_page' => '1',
                'page' => '',
                'data' => $map->limit((int) $data['limit_num'],(int) $data['limit'])->select()->toArray(),
            ];
        }else{
            $getlist = $map->paginate($limit);
            $page = $getlist->render();
            $getlist = Common::JobArray($getlist);
            $getlist['page'] = $page;
        }
        return $getlist;
    }
    //  获取列表
    public function getList($data){
        if(empty($data['mid']) || (!empty($data['mid']) && is_array($data['mid']) && count($data['mid']) > 1)){
            $getlist = $this->getBaseList($data);
            $_getlist = $getlist['data'];
            if(!empty($_getlist)){
                $_mid = array_unique(array_column($_getlist,'mid'));
                $_data = [];
                foreach ($_mid as $k => $v){
                    $_v = [
                        'id' => array_column(Common::del_file($_getlist,'mid',$v),'id'),
                        'mid' => $v,
                        'getdeltime' => !isset($data['getdeltime']) ? null : $data['getdeltime'],
                        'del_time' => !isset($data['del_time']) ? null : $data['del_time'],
                        'status' => !isset($data['status']) ? null : $data['status'],
                        'limit' => count(array_column(Common::del_file($_getlist,'mid',$v),'id')),
                        'page' => '1',
                    ];
                    $_v = $this->getTableKey($_v);
                    $_data = array_merge($_data,$_v['data']);
                }
                foreach ($_getlist as $k => $v){
                    $_v = Common::del_file($_data,'id',$v['id']);
                    $_getlist[$k] = $_v['0'];
                }
                $getlist['data'] = $_getlist;
            }
        }else{
            $data['mid'] = is_array($data['mid']) ? $data['mid']['0'] : $data['mid'];
            $getlist = $this->getTableKey($data);
        }
        if($getlist['total'] < '1'){
           return $getlist;
        }
        $_getlist = $getlist['data'];
        if(!empty($_getlist)){
            //  获取所有栏目
            $_partist = array_unique(array_column($_getlist,'fid'));
            $_partist = Db::name($this->model_key."_part")->whereIn('id',$_partist)->select()->toArray();
            //  获取所有模型
            $_midlist = array_unique(array_column($_getlist,'mid'));
            $_modelist = Db::name($this->model_key."_model")->whereIn('id',$_midlist)->select()->toArray();
            //  解读字段
            $_filelist = Db::name($this->model_key."_filed")->whereIn('mid',$_midlist)->whereStatus('1')->whereDelTime('0')->order('sort desc,id asc')->select()->toArray();
            foreach ($_filelist as $k => $v){
                $_v = Common::ReadFile($v);
                $_v['mid'] = $v['mid'];
                $_filelist[$k] = $_v;
            }
            //  获取评论
            $_mids = array_unique(array_column($_getlist,'mid'));
            $_aids = array_unique(array_column($_getlist,'id'));
            $_commentlist = Db::name("comment")->whereModel($this->model_key)->whereType('aid')->whereIn('aid',$_aids)->whereIn('mid',$_mids)->field('aid,mid')->select()->toArray();
            //  获取专题
            $_sid = Db::name('special_article')->whereModel($this->model_key)->whereIn('aid',$_aids)->field('aid,sid')->select()->toArray();
            if(!empty($_sid)){
                $_sid_ = array_unique(array_column($_sid,'sid'));
                $_sid_ = Db::name('special')->whereStatus('1')->whereIn('id',$_sid_)->field('id,title')->select()->toArray();
                foreach ($_sid as $k => $v){
                    $_v = Common::del_file($_sid_,'id',$v['sid']);
                    if(empty($_v)){
                        unset($_sid[$k]);
                        continue;
                    }
                    $v['special_name'] = $_v['0']['title'];
                    $_sid[$k] = $v;
                }
            }
            //  获取辅助栏目
            $_fuid = Db::name($this->model_key."_fupart_article")->whereIn('aid',$_aids)->field('aid,fuid')->select()->toArray();
            if(!empty($_fuid)){
                $_fuid_ = array_unique(array_column($_fuid,'fuid'));
                $_fuid_ = Db::name($this->model_key."_fupart")->whereIn('id',$_fuid_)->field('id,title')->select()->toArray();
                foreach ($_fuid as $k => $v){
                    $_v = Common::del_file($_fuid_,'id',$v['fuid']);
                    if(empty($_v)){
                        unset($_fuid[$k]);
                        continue;
                    }
                    $v['fupart_name'] = $_v['0']['title'];
                    $_fuid[$k] = $v;
                }
            }
            //  获取订单信息
            $_order_mid = Common::del_file($_modelist,'order','1');
            $_order_aid = empty($_order_mid) ? [] : Common::del_file($_getlist,'mid',array_column($_order_mid,'id'));
            $_china_group = $_user_group = $_quanlist = [];
            if(!empty($_order_aid)){
                $orModel = new Order();
                $_ormap = ['aid' => array_column($_order_aid,'id'),'mid' => array_column($_order_mid,'id'),];
                $_order_aid = $orModel->getList($_ormap,$this->model_key);
                $_group_mid = Common::del_file($_order_mid,'order_group',['1','2','4']);
                if(count($_group_mid) > '0') {
                    $_user_group =  Common::del_file($_order_mid,'order_group',['1','2']);
                    $_china_group =  Common::del_file($_order_mid,'order_group','4');
                    if(!empty($_user_group)){
                        $uModel = new UserGroup();
                        $_user_group = $uModel->getList();
                    }
                    if(!empty($_china_group)){
                        $_chinalist = Common::del_file($_partist, 'id', array_unique(array_column($_getlist,'fid')));
                        $_chinalist = array_column($_chinalist,'chinalist');
                        $_chinalist = Common::del_null($_chinalist);
                        if(!empty($_chinalist)) {
                            $_vc = [];
                            foreach ($_chinalist as $k => $v) {
                                $_vc = array_merge($_vc, explode(',', $v));
                            }
                            $_chinalist = array_unique($_vc);
                        }
                        $_chinalist = empty($_chinalist) ? [] : Common::del_null($_chinalist);
                        if(!empty($_chinalist)) {
                            $uModel = new Chinacode();
                            $_chinalist = $uModel->getList(['zoneid' => $_chinalist]);
                        }
                        $_china_group = NodeFormat::config(['id' => 'zoneid', 'pid' => 'parzoneid', 'title' => 'zonename'])->toList($_chinalist);
                    }
                }
                //  查询优惠券是否存在
                $orModel = new ModelList();
                $_qlist = $orModel->getList(['keys' => 'quan']);
                if($_qlist['total'] > '0'){
                    $orModel = '\\app\\common\\model\\quan\\Coupon';
                    $orModel = new $orModel;
                    $_qlist = $orModel->getList(['model_list' => $this->model_key]);
                    $_quanlist = $_qlist['total'] > '0' ? $_qlist['data'] : [];
                }
            }
            $_getlist = getUserList($_getlist);

            foreach ($_getlist as $k => $v){
                $_v = Common::del_file($_filelist,'mid',$v['mid']);
                $_v = Common::getReadFile($_v,$v);
                $v = array_merge($v,$_v);
                $_v = Common::del_file($_commentlist,'aid',$v['uid']);
                $_v = Common::del_file($_v,'mid',$v['id']);
                $v['user_comment_num'] = count($_v);
                $_vf = Common::del_file($_partist,'id',$v['fid']);
                $v['comment_see'] = empty($_vf['0']['comment_see']) ? '0' : $_vf['0']['comment_see'];
                $v['part_name'] = empty($_vf['0']['title']) ? '栏目已删除' : $_vf['0']['title'];
                $_vm = Common::del_file($_modelist,'id',$v['mid']);
                $v['comment_see'] = empty($_vm['0']['see_comment']) ? '0' : $_vm['0']['see_comment'];
                //  查询专题
                if(!empty($_sid)){
                    $_v = Common::del_file($_sid,'aid',$v['id']);
                    $v['sid'] = implode(',',array_column($_v,'sid'));
                    $v['special'] = $_v;
                }
                //  查询专题
                if(!empty($_fuid)){
                    $_v = Common::del_file($_fuid,'aid',$v['id']);
                    $v['fuid'] = implode(',',array_column($_v,'fuid'));
                    $v['fupart'] = $_v;
                }
                //  检测是否启用订单
                if(!empty($_vm['0']['order']) && $_vm['0']['order'] == '1'){
                    $v['_order_group'] = $_vm['0']['order_group'];
                    $_vo =  Common::del_file($_order_aid,'mid',$v['mid']);
                    $_vo =  Common::del_file($_vo,'aid',$v['id']);
                    if(!empty($_vo)){
                        $_money = Common::del_file($_vo,'groupid','0');
                        $_money = Common::del_file($_money,'parameter','0');
                        $_money = Common::del_file($_money,'chinacode','0');
                        $v['stock_type'] = empty($_money['0']['stock_type']) ? '0' : $_money['0']['stock_type'];
                        $v['stock'] = empty($_money['0']['stock']) ? '0' : $_money['0']['stock'];
                        $v['money'] = empty($_money['0']['money']) ? '0' : $_money['0']['money'] / 100;
                        $v['money_zk'] = empty($_money['0']['money_zk']) ? '0' : $_money['0']['money_zk'] / 100;
                        $v['sale_one'] = empty($_money['0']['sale_one']) ? '0' : $_money['0']['sale_one'] / 100;
                        $v['sale_two'] = empty($_money['0']['sale_two']) ? '0' : $_money['0']['sale_two'] / 100;
                        $v['sale_three'] = empty($_money['0']['sale_three']) ? '0' : $_money['0']['sale_three'] / 100;
                    }
                    $_vname = $_vm['0']['order_group'] == '4' ? 'chinacode' : 'groupid';
                    $_usergroup = $_vm['0']['order_group'] == '4' ? $_china_group : $_user_group;
                    if(in_array($_vm['0']['order_group'],['1','2','4'])) {
                        if(!empty($_usergroup)) {
                            foreach ($_usergroup as $k1 => $v1) {
                                $v1['id'] = $_vm['0']['order_group'] == '4' ? $v1['zoneid'] : $v1['id'];
                                $_vol = Common::del_file($_vo,$_vname,$v1['id']);
                                $v[$_vname.'_'.$v1['id']] = empty($_vol['0']['money']) ? '0' : $_vol['0']['money'] / 100;
                                $v[$_vname.'_'.$v1['id'].'money_zk'] = empty($_vol['0']['money_zk']) ? '0' : $_vol['0']['money_zk'] / 100;
                                $v[$_vname.'_'.$v1['id'].'sale_one'] = empty($_vol['0']['sale_one']) ? '0' : $_vol['0']['sale_one'] / 100;
                                $v[$_vname.'_'.$v1['id'].'sale_two'] = empty($_vol['0']['sale_two']) ? '0' : $_vol['0']['sale_two'] / 100;
                                $v[$_vname.'_'.$v1['id'].'sale_three'] = empty($_vol['0']['sale_three']) ? '0' : $_vol['0']['sale_three'] / 100;
                            }
                        }
                    }
                    if(!empty($_vf['0']['order_level']) && $_vf['0']['order_level'] == '1'){
                        $_vop = Common::del_file($_vo,'parameter','0',true);
                        $_vopl = array_unique(array_column($_vop,'parameter'));
                        $_parameter = [];
                        foreach ($_vopl as $k1 => $v1) {
                            $_vmoney = Common::del_file($_vop, 'parameter', $v1);
                            $_money = Common::del_file($_vmoney, 'groupid', '0');
                            $_money = Common::del_file($_money, 'chinacode', '0');
                            $_v1 = [
                                'parameter' => $v1,
                                'stock_type' => empty($_money['0']['stock_type']) ? '0' : $_money['0']['stock_type'],
                                'stock' => empty($_money['0']['stock']) ? '0' : $_money['0']['stock'],
                                'money' => empty($_money['0']['money']) ? '0' : $_money['0']['money'] / 100,
                                'money_zk' => empty($_money['0']['money_zk']) ? '0' : $_money['0']['money_zk'] / 100,
                                'sale_one' => empty($_money['0']['sale_one']) ? '0' : $_money['0']['sale_one'] / 100,
                                'sale_two' => empty($_money['0']['sale_two']) ? '0' : $_money['0']['sale_two'] / 100,
                                'sale_three' => empty($_money['0']['sale_three']) ? '0' : $_money['0']['sale_three'] / 100,
                                'sort' => empty($_money['0']['sort']) ? '0' : $_money['0']['sort'],
                            ];
                            if(!empty($_usergroup) && in_array($_vm['0']['order_group'],['1','2','4'])) {
                                foreach ($_usergroup as $k2 => $v2) {
                                    $v2['id'] = $_vm['0']['order_group'] == '4' ? $v2['zoneid'] : $v2['id'];
                                    $_vol = Common::del_file($_vmoney,$_vname,$v2['id']);
                                    $_v1[$_vname.'_'.$v2['id']] = empty($_vol['0']['money']) ? '0' : $_vol['0']['money'] / 100;
                                    $_v1[$_vname.'_'.$v2['id'].'money_zk'] = empty($_vol['0']['money_zk']) ? '0' : $_vol['0']['money_zk'] / 100;
                                    $_v1[$_vname.'_'.$v2['id'].'sale_one'] = empty($_vol['0']['sale_one']) ? '0' : $_vol['0']['sale_one'] / 100;
                                    $_v1[$_vname.'_'.$v2['id'].'sale_two'] = empty($_vol['0']['sale_two']) ? '0' : $_vol['0']['sale_two'] / 100;
                                    $_v1[$_vname.'_'.$v2['id'].'sale_three'] = empty($_vol['0']['sale_three']) ? '0' : $_vol['0']['sale_three'] / 100;
                                }
                            }
                            array_push($_parameter,$_v1);
                        }
                        $v['parametermoney'] = Common::ArraySort($_parameter,'sort','desc');
                    }
                    //  检测是否存在优惠券
                    if(!empty($_quanlist)){
                        $_vq = [];
                        $v['money'] = empty($v['money']) ? '0' : $v['money'];
                        $_v1m = empty($v['money_zk']) ? $v['money'] : $v['money_zk'];
                        foreach ($_quanlist as $k1 => $v1){
                            if($v1['time_type'] == '0' && (time() > strtotime($v1['end_time']) || time() < strtotime($v1['add_time']))){
                                continue;
                            }
                            if($v1['zonenum'] > '0' && $v1['zone_num'] < '1'){
                                continue;
                            }
                            if(!empty($v1['model_limit']) == '1' && !in_array($this->model_key,explode(',',$v1['model_list']))){
                                continue;
                            }
                            if(!empty($v1['article_limit']) == '1' && !in_array($v['id'],explode(',',$v1['article_list']))){
                                continue;
                            }
                            if(session('userdb') && !empty($v1['group']) && !in_array(session('userdb.u_groupid'),explode(',',$v1['group']))){
                                continue;
                            }
                            if($v1['type'] == '1' && $_v1m < $v1['minmoney']){
                                continue;
                            }
                            $_vq[] = $v1;
                        }
                        $v['_quanlist'] = $_vq;
                    }
                    if(!empty($_paylist)){
                        $_v = Common::del_file($_paylist,'mid',$v['mid']);
                        $_v = Common::del_file($_v,'aid',$v['id']);
                        $v['pay_num'] = count($_v);
                    }
                }

                $v['u_name'] = empty($v['userdb']['u_name']) ? '' : $v['userdb']['u_name'];
                $v['u_uniname'] = empty($v['userdb']['u_uniname']) ? '' : $v['userdb']['u_uniname'];
                $v['u_uname'] = empty($v['userdb']['u_uname']) ? '' : $v['userdb']['u_uname'];
                $v['u_icon'] = empty($v['userdb']['u_icon']) ? '' : $v['userdb']['u_icon'];
                unset($v['userdb']);
                $v['keywordslist'] = empty($v['keywords']) ? [] : explode(',',$v['keywords']);
                if(!empty($v['keywordslist'])){
                    foreach ($v['keywordslist'] as $k1 => $v1){
                        $v['keywordslist'][$k1] = "<a target='_blank' title='{$v1}' href='http://open.wormcms.com/'>{$v1}</a>";
                    }
                }
                $v['time_date'] = date('Y-m-d', (int) $v['addtime']);
                $v['time_Y'] = date('Y', (int) $v['addtime']);
                $v['time_m'] = date('m', (int) $v['addtime']);
                $v['time_d'] = date('d', (int) $v['addtime']);
                $v['time_his'] = date('H:i:s', (int) $v['addtime']);
                $v['time_H'] = date('H', (int) $v['addtime']);
                $v['time_i'] = date('i', (int) $v['addtime']);
                $v['time_s'] = date('s', (int) $v['addtime']);
                $v['addtime'] = date('Y-m-d H:i:s', (int) $v['addtime']);
                $v['uri'] = url("/{$this->model_key}/article-{$v['id']}")->build();
                $_getlist[$k] = $v;
            }
            $getlist['data'] = $_getlist;
        }
        return $getlist;
    }
    //  获取单一数据
    public function getOne($mid,$id,$uid = ''){
        $getlist = $this->getList(['mid' => $mid,'id' => $id,'status' => 'a']);
        return $getlist['total'] > '0' ? $getlist['data']['0'] : [];
    }
    //  清洗数据
    public function getEditAdd($data = []){
        $fileList = Db::name($this->base_table."_{$data['mid']}")->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            continue;
        }
        $old = empty($_data['id']) ? [] : Db::name($this->base_table."_{$data['mid']}")->whereId($_data['id'])->find();
        if(empty($old)){
            unset($_data['id']);
        }else{
            $_data['edittime'] = time();
            $_data['editip'] = Request::ip();
            $old = Upload::editadd($old,false);
        }
        $_data['keywords'] = empty($_data['keywords']) ? null : str_replace([' ','，'],',',$_data['keywords']);
        $_data['status'] = empty($_data['status']) ? '0' : $_data['status'];
        $_data['del_time'] = empty($old['del_time']) ? '0' : $old['del_time'];
        $_data['del_time'] = !empty($_data['status']) ? '0' : $_data['del_time'];
        $_data['addtime'] = empty($old['addtime']) ? time() : $old['addtime'];
        $_data['addip'] = empty($old['addip']) ? Request::ip() : $old['addip'];
        $_data['sort'] = empty($_data['sort']) ? $_data['addtime'] : $_data['sort'];
        $_data['content'] = empty($_data['content']) ? '' : Common::data_html($_data['content']);

        if(empty($_data['description'])){
            $_data['description'] = str_replace(PHP_EOL, '', $_data['content']);
            $_data['description'] = preg_replace('/<([^<]*)>/is',"",$_data['description']);
            $_data['description'] = preg_replace('/ |　|&nbsp;/is',"",$_data['description']);	//把多余的空格去除掉
            $_data['description'] = preg_replace('/\s/is',"",$_data['description']);
            $_data['description'] = get_word($_data['description'],300,false);	//把多余的空格去除掉
        }
        $_data['title'] = empty($_data['title']) ? get_word($_data['description'],20,false) : $_data['title'];
        $_data['picurl'] = $this->setFile(empty($_data['picurl']) ? null : $_data['picurl'], empty($old['picurl']) ? null : $old['picurl']);
        if(empty($_data['picurl'])){
            $img_data = Upload::img_list($_data['content']);
            $_data['picurl'] = empty($img_data) ? $_data['picurl'] : Upload::fileMove($img_data['0'],'','copy',false);
        }
        $_data = Upload::editadd($_data);
        return $_data;
    }
    protected function getBaseEditAdd($data){
        $fileList = $this->getTableFields();
        $_data = [];
        foreach ($fileList as $k => $v){
            if(isset($data[$v])){
                $_data[$v] = $data[$v];
            }
            if($v == 'pick' && !empty($data['picurl'])){
                $_data[$v] = '1';
            }
            continue;
        }
        return $_data;
    }
    //  保存数据
    public function setOne($data){
        $add = $this->getBaseEditAdd($data);
        if(empty($data['id'])){
            if(!$add = $this->create($add)){
                return false;
            }
            $data['id'] = $add->id;
            if(!Db::name($this->base_table."_{$data['mid']}")->insert($data)){
                $add->delete();
                return false;
            }
        }else{
            $old = $this->whereId($add['id'])->find();
            $_old = $old->toArray();
            $basesave = false;
            $_old['del_time'] = empty($_old['del_time']) ? '0' : strtotime($_old['del_time']);
            unset($_old['addtime']);
            foreach ($_old as $k => $v){
                if($k == 'addtime' || !isset($add[$k])){
                    continue;
                }
                if($v != $add[$k]){
                    $basesave = true;
                }
                $_old[$k] = $add[$k];
            }
            if($basesave){
                if(!$this->whereId($_old['id'])->update($_old)){
                    return false;
                }
            }
            unset($data['part_name']);
            Db::name($this->base_table."_{$data['mid']}")->whereId($data['id'])->update($data);
        }
        $res = $this->whereId(empty($add->id) ? $data['id'] : $add->id)->find()->toArray();
        return $res;
    }
    //  快速编辑
    public function FastSwitch($data){
        $this->whereId($data['id'])->update([$data['_field'] => $data[$data['_field']]]);
        $_old = $this->whereId($data['id'])->find();
        if(Db::name($this->base_table."_{$_old['mid']}")->whereId($_old['id'])->update([$data['_field'] => $data[$data['_field']]])){
            return true;
        }
        return false;
    }
    //  处理文件
    protected function setFile($new,$old){
        $old = empty($old) ? null : $old;
        if($new != $old){
            if(!empty($new)){
                $new = Upload::fileMove($new);
            }
            if(!empty($old)){
                Upload::fileDel($old);
            }
        }
        return $new;
    }
    //  删除内容
    public function DeleteOne($id){
        $_oldlist = $this->getList(['id' => $id,'status' => 'a','getdeltime' => '1001']);
        $_oldlist = $_oldlist['total'] > '0' ? $_oldlist['data'] : [];
        if(empty($_oldlist)){
            return true;
        }
        //  获取是否在回收站
        $_deltime = Common::del_file($_oldlist,'del_time','0');
        $mid = array_unique(array_column($_oldlist,'mid'));
        if(!empty($_deltime)){
            $add = [
                'status' => '0',
                'del_time' => time(),
            ];
            $this->whereIn('id',$id)->update($add);
            foreach ($mid as $k => $v){
                Db::name($this->base_table."_{$v}")->whereIn('id',$id)->update($add);
                Db::name('special_article')->whereModel($this->model_key)->whereIn('id',$id)->update($add);
            }
        } else {
            //  获取字段
            $filelist = Db::name($this->model_key."_filed")->whereIn('mid',$mid)->select()->toArray();
            $filelist = Common::del_file($filelist,'form_type',['upload_img','editor','upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr']);
            if(!empty($filelist)){
                $editor = [];
                foreach ($filelist as $k => $v){
                    $filelist[$k] = Common::ReadFile($v);
                    if($filelist[$k]['type'] == 'editor'){
                        $editor[] = $filelist[$k]['file'];
                    }
                }
                $_delfile = [];
                foreach ($_oldlist as $k => $v){
                    $_v = Common::getReadFile($filelist,$v);
                    if(!empty($editor)){
                        foreach ($editor as $k1 => $v1){
                            if(!empty($_v[$v1])){
                                $_delfile[] = Upload::img_list($_v[$v1]);
                                unset($_v[$v1]);
                            }
                            continue;
                        }
                    }
                    $_delfile[] = $_v;
                }
                $_delfile = Common::del_null($_delfile);
                Upload::fileDel($_delfile);
            }
            $this->whereIn('id',$id)->delete();
            foreach ($mid as $k => $v) {
                Db::name($this->base_table . "_{$v}")->whereIn('id',$id)->delete();
                Db::name('special_article')->whereModel($this->model_key)->whereIn('id',$id)->delete();
            }
        }
        return true;
    }
    //  还原内容
    public function TrashOne($data){
        $_mid = $this->whereIn('id',$data)->column('mid');
        if($this->whereIn('id',$data)->update(['del_time' => '0','status' => '1'])){
            $_mid = array_unique($_mid);
            foreach ($_mid as $k => $v){
                Db::name($this->base_table."_{$v}")->whereIn('id',$data)->update(['del_time' => '0','status' => '1']);
                Db::name('special_article')->whereModel($this->model_key)->whereIn('id',$data)->update(['del_time' => '0','status' => '1']);
            }
            return true;
        }
        return false;
    }
    //  查询前后篇
    public function getUNpage($data){
        $_data = $this->whereFid($data['fid'])->where('id','>',$data['id'])->whereStatus('1')->whereDelTime('0')->order('id desc')->find();
        $articlepage['prev'] = empty($_data) ? [] : $this->getOne($_data['mid'],$_data['id']);
        $_data = $this->whereFid($data['fid'])->where('id','<',$data['id'])->whereStatus('1')->whereDelTime('0')->order('id asc')->find();
        $articlepage['next'] = empty($_data) ? [] : $this->getOne($_data['mid'],$_data['id']);
        return $articlepage;
    }
    //  内容聚合
    public function getHousehole($data){
        $map = Db::name($this->base_table."_{$data['mid']}")->distinct(true)->field($data['filed'])->whereRaw("`{$data['filed']}` is not null and LENGTH(trim({$data['filed']}))>0");
        $map = !empty($data['key']) ? $map->whereLike($data['filed'],"%{{$data['key']}}%") : $map;
        $map = !empty($data['fid']) ? $map->whereIn('fid',$data['fid']) : $map;
        $map = !empty($data['uid']) ? $map->whereIn('uid',$data['uid']) : $map;
        $map = !empty($data['id']) ? $map->whereIn('id',$data['id']) : $map;
        if(!isset($data['status'])){
            $map = $map->whereStatus('1');
        }else if(isset($data['status']) && $data['status'] != 'a'){
            $map = $map->whereStatus($data['status']);
        }
        $limit = [
            'list_rows' => empty($data['limit']) ? '24' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->paginate($limit);
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            $_filed = array_column($getlist['data'],$data['filed']);
            //  获取聚合内容
            $map = Db::name($this->base_table."_{$data['mid']}")->whereIn($data['filed'],$_filed)->field("{$data['filed']},id,hist,comment_num");
            if(!isset($data['status'])){
                $map = $map->whereStatus('1');
            }else if(isset($data['status']) && $data['status'] != 'a'){
                $map = $map->whereStatus($data['status']);
            }
            $_oldlist = $map->select()->toArray();
            foreach ($_filed as $k => $v){
                $_v = Common::del_file($_oldlist,$data['filed'],$v);
                $_hist = array_sum(array_column($_v,'hist'));
                $_comment_num = array_sum(array_column($_v,'comment_num'));
                $_id = array_column($_v,'id');
                $_filed[$k] = [
                    'id' => $v,
                    'title' => $v,
                    'hist' => $_hist,
                    'comment_num' => $_comment_num,
                    'total' => count($_id),
                ];
            }
            $getlist['data'] = $_filed;
        }
        return $getlist;
    }
}