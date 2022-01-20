<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;

class Comment extends AdminBase
{
    use AddEditList;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\Comment";
        $this->validate = "app\\common\\validate\\Comment";
        $this->contauth = $this->CheckAuth('create,edit,del');
        $this->model = new $models;
        $this->getConf();
    }
    protected function getMap()
    {
        $map = [
            'aid' => empty($this->getdata['aid']) ? '' : $this->getdata['aid'],
            'type' => empty($this->getdata['type']) ? '' : $this->getdata['type'],
            'status' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
        ];
        if(!empty($this->getdata['pid'])){
            array_push($map,['pid' => $this->getdata['pid']]);
        }
        if(!empty($this->getdata['type']) && $this->getdata['type'] == 'oid'){
            if(empty($this->getdata['typeedit'])){
                $map['oid'] = $this->getdata['aid'];
                unset($map['aid']);
            }
        }
        return $map;
    }
    protected function getConf(){
        if($this->request->action() == 'index'){
            $this->list_base['uri'] = url('getlist',['status' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status']])->build();
            $this->list_nav = [
                'list' => [
                    ['id' => 'a', 'title' => '全部', 'uri' => url('index', ['status' => 'a'])->build()],
                    ['id' => '0', 'title' => '待审核', 'uri' => url('index', ['status' => '0'])->build()],
                    ['id' => '1', 'title' => '已通过', 'uri' => url('index', ['status' => '1'])->build()],
                    ['id' => '2', 'title' => '已拒绝', 'uri' => url('index', ['status' => '2'])->build()],
                ],
                'default' => !isset($this->getdata['status']) ? 'a' : $this->getdata['status'],
            ];
            $this->list_file = [
                ['type' => 'checkbox','textalign' => 'center','fixed' => 'left','width' => '40'],
                ['file' => 'id','title' => 'ID','type' => 'text','width' => '80','fixed' => 'left','textalign' => 'center'],
                ['file' => 'u_name','title' => '用户名','type' => 'text','width' => '12%'],
                ['file' => 'model','title' => '模块','type' => 'text','width' => '8%','textalign' => 'center'],
                ['file' => 'type', 'title' => '类型', 'type' => 'radio', 'data' => ['aid' => '内容','uid' => '用户','oid' => '订单'], 'width' => '5%'],
                ['file' => 'art_title','title' => '文章标题','type' => 'text','width' => '20%'],
                ['file' => 'content','title' => '评论内容','type' => 'text','width' => '30%'],
                ['file' => 'addtime','title' => '时间','type' => 'text','width' => '180'],
                ['file' => 'jian','title' => '精华','type' => 'switch','text' => '推荐|未推荐','textalign' => 'center','width' => '100','default' => '1'],
                ['file' => 'status','title' => '状态','type' => 'switch','text' => '通过|待审','textalign' => 'center','width' => '100','default' => '1'],
            ];
            if($this->contauth['edit']){
                $this->list_file[] = ['filed' => 'edit','title' => '编辑','type' => 'btn','text' => true,'open' => true,'opentitle' => "编辑链接",'uri' => url('edit',['id' => '__id__'])->build(),'icon' => 'cx-iconbianji3 cx-text-f16','class' => 'cx-text-green','textalign' => 'center','fixed' => 'right','width' => '80'];
            }
            if($this->contauth['del']){
                $this->list_file[] = ['filed' => 'del','title' => '删除','type' => 'btn','event' => 'del','text' => true,'icon' => 'cx-iconlajixiang cx-text-f16','class' => 'cx-text-red','textalign' => 'center','fixed' => 'right','width' => '80'];
                $this->list_top = [
                    ['title' => '批量删除','event' => 'pdel', 'class' => 'cx-button-s cx-bg-red cx-mag-r10', 'uri' => url('pdel')->build()],
                ];
            }
        }else if(in_array($this->request->action(),['edit'])){
            $this->list_base['uri'] = url('authrule/save',['id' => $this->getdata['id']]);
            $this->list_temp = 'read';
        }
    }
}