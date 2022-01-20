<?php
declare(strict_types = 1);

namespace app\home\controller;

use app\common\controller\HomeBase;
use app\common\model\ModelList;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use think\facade\Cache;
use think\facade\Db;

class Search extends HomeBase
{
    use AddEditList;
    protected $ModelList;
    protected $RModelList;
    protected $Page;
    protected $Limit;
    protected function initialize()
    {
        parent::initialize();
        $this->ModelList = !empty($this->getdata['m']) ? [['keys' => $this->getdata['m']]] : $this->getModel($this->getdata);
        $this->RModelList = $this->getAllModel($this->ModelList);
    }
    public function index(){
        //  记录分页
        $this->Page = empty($this->getdata['page']) ? '1' : $this->getdata['page'];
        $this->Limit = empty($this->getdata['limit']) ? '20' : $this->getdata['limit'];
        //  进行内容搜索
        $getlist = empty($this->getdata['key']) ? [] : $this->getArticleList($this->RModelList);
        $this->list_temp = $this->hasview("search.htm");
        if(!is_file($this->list_temp)){
            $this->error("模板文件【{$this->list_temp}】不存在");
        }
        return $this->viewHomeList($getlist);
    }
    //  查询可用模块
    protected function getModel($data){
        $_model = new ModelList();
        $res = $_model->getList(['class' => '0','keys' => !empty($data['m']) ? $data['m'] : []],true);
        return $res;
    }
    //  查询所有可用模型
    protected function getAllModel($data){
        $_data = [];
        foreach ($data as $k => $v){
            $_model = "app\\common\\model\\" . $v['keys'] . "\Artmodel";
            $_model = new $_model;
            $_v = $_model->getList(['id' => !empty($this->getdata['mid']) && $this->getdata['m'] == $v['keys'] ? $this->getdata['mid'] : '']);
            $_v = array_column($_v,'id');
            if(empty($_v)){
                continue;
            }
            $_model = "app\\common\\model\\" . $v['keys'] . "\Artmodelfile";
            $_model = new $_model;
            $_vf = $_model->getList(['mid' => $_v]);
            foreach ($_v as $k1 => $v1){
                $_v1 = Common::del_file($_vf,'mid',$v1);
                $_v1 = array_column($_v1,'sql_file');
                if(empty($_v1)){
                    continue;
                }
                array_push($_v1,'keywords');
                $_v[$k1] = [
                    'mid' => $v1,
                    'filed' => implode('|',$_v1)
                ];
            }
            $_data[] = [
                'm' => $v['keys'],
                'data' => $_v
            ];
        }
        return $_data;
    }
    //  开始搜索内容
    protected function getArticleList($data){
        if(Cache::has("search_{$this->getdata['key']}")){
            $_res = Cache::get("search_{$this->getdata['key']}");
        }else{
            $_res = $this->setArticleCache($data);
        }
        //  对结果进行分页
        $start = ($this->Page - 1) * $this->Limit;
        $_alist = array_slice($_res['data'],$start,$this->Limit);
        //  重新获取模块信息
        $_mod = array_unique(array_column($_alist,'_m_'));
        $last_page = $_res['count'] / $this->Limit;
        $res = [
            'total' => $_res['count'],
            'per_page' => $this->Limit,
            'current_page' => $this->Page,
            'last_page' => (int) ceil($last_page),
            'data' => [],
        ];
        foreach ($_mod as $k => $v){
            $_v =  Common::del_file($_alist,'_m_',$v);
            $_va = [
                '_m_' => $v,
                'mid' => array_unique(array_column($_v,'mid')),
                'id' => array_unique(array_column($_v,'id')),
            ];
            $_va = $this->getArticleListData($_va);
            $res['data'] = array_merge($res['data'],$_va);
        }
        if(count($res['data']) < $res['per_page'] && $res['per_page'] * $res['current_page'] < $res['total']){
            $this->setArticleCache($data,$_res['count'] + 1);
            return $this->getArticleList($data);
        }
        return $res;
    }
    //  储存搜索索引
    protected function setArticleCache($data,$page = 1){
        $_res = [];
        $_count = 0;
        foreach ($data as $k => $v){
            foreach ($v['data'] as $k1 => $v1){
                $v1['m'] = $v['m'];
                $_v = $this->getOneModel($v1);
                if($_v['count'] < 1){
                    continue;
                }
                $_count = $_v['count'] + $_count;
                $_res = array_merge($_res,$_v['list']);
            }
        }
        $_res = [
            'count' => $_count,
            'page' => $page,
            'data' => Common::arraySort($_res,'addtime')
        ];
        if($page > 1){
            $_ores = cache("search_{$this->getdata['key']}");
            $_res['data'] = array_merge($_res['data'],$_ores['data']);
        }
        cache("search_{$this->getdata['key']}",$_res);
        return $_res;
    }
    //  分表搜索
    protected function getOneModel($data){
        $start = ($this->Page - 1) * $this->Limit;
        $ids = Db::name("{$data['m']}_content_{$data['mid']}")->whereLike($data['filed'],"%{$this->getdata['key']}%")->whereStatus('1')->field('id,addtime')->order('id desc')->limit($start,(int) $this->Limit)->select()->toArray();
        $count = Db::name("{$data['m']}_content_{$data['mid']}")->whereLike($data['filed'],"%{$this->getdata['key']}%")->whereStatus('1')->field('id,addtime')->order('id desc')->count();
        foreach ($ids as $k => $v){
            $ids[$k]['mid'] = $data['mid'];
            $ids[$k]['_m_'] = $data['m'];
        }
        $res = [
            'count' => $count,
            'list' => $ids,
        ];
        return $res;
    }
    //  获取内容详情
    protected function getArticleListData($data){
        $_model = "app\\common\\model\\" . $data['_m_'] . "\Article";
        $_model = new $_model;
        $res = $_model->getList(['mid' => $data['mid'],'id' => $data['id'],'page' => '1','limit' => count($data['id'])]);
        return $res['data'];
    }
}