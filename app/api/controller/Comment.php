<?php
declare(strict_types = 1);

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\model\UserMoney;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\exception\ValidateException;
use think\facade\Db;

class Comment extends ApiBase
{
    use AddEditList;
    protected $basename;
    protected $ArticleModel;
    protected function initialize(){
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/',toUnderScore(get_called_class()),$array);
        $this->basename = $array['0']['2'];
        $mdodel = "app\\common\\model\\Comment";
        $this->model = new $mdodel;
        $ArticleModel = "app\\common\\model\\".$this->getdata['models']."\Article";
        $this->ArticleModel = new $ArticleModel;
        $this->validate = "app\\common\\validate\\ArtComment";
    }
    protected function getMap()
    {
        $map = [
            'model' => $this->getdata['models'],
            'aid' => $this->getdata['aid'],
            'type' => $this->getdata['type'],
            'pid' => empty($this->getdata['pid']) ? '0' : $this->getdata['pid'],
        ];
        if($this->getdata['type'] == 'oid'){
            if(empty($this->getdata['typeedit'])){
                $map['oid'] = $this->getdata['aid'];
                unset($map['aid']);
            }
        }
        return $map;
    }

    public function save()
    {
        if(!$this->isLogin()){
            $this->result('','10000','请登录');
        }
        if(!$this->request->isPost() || !$this->isLogin()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        if($data['aid'] != $this->getdata['aid']){
            $this->result('','0','非法访问');
        }
        $data['uid'] = $this->wormuser['uid'];
        $data['model'] = $this->getdata['models'];
        $data['pid'] = empty($data['pid']) ? '0' : $data['pid'];
        $data['status'] = $this->webdb["auto_comment"];
        if($data['type'] == 'aid'){
            $_article = $this->ArticleModel->whereId($data['aid'])->find();
            if(empty($_article) || $_article['status'] != '1' || $_article['del_time'] > '0'){
                $this->result('','0','此内容禁止评论');
            }
            $_article = $this->ArticleModel->getOne($_article['mid'],$_article['id']);
            if($_article['comment_see'] != '1'){
                $this->result('','0','此内容禁止评论1');
            }
            $data['mid'] = $_article['mid'];
        }else if($data['type'] == 'uid'){
            if($data['aid'] == $this->wormuser['uid']){
                $this->result('','0','不能评价自己');
            }
            if(!empty($data['money'])){
                $addmoney = [
                    'oid' => $data['mid'],
                    'uid' => $data['aid'],
                    'puid' => '0',
                    'add_type' => '1000',
                    'money_type' => '0',
                    'money' => $data['money'],
                    'cont' => $data['content'],
                    'addtime' => time(),
                    'addip' => $this->request->ip(),
                ];
                $umModel = new UserMoney();
                $umModel->new_add($addmoney);
                $data['status'] = '1';
            }
        }
        try {
            validate($this->validate)->scene("add")->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        if(!empty($data['imgs'])){
            $dir = 'comment/'.date('Y-m',time());
            $_imgs = [];
            foreach ($data['imgs'] as $k => $v){
                if(empty($v['uri'])){
                    unset($data['imgs'][$k]);
                    continue;
                }
                $v['uri'] = Upload::fileMove($v['uri'],$dir);
                $_imgs[] = $v;
            }
            $data['imgs'] = Upload::editadd($_imgs);
        }
        $_data = $this->model->getEditAdd($data);
        if($add = $this->model->setOne($_data)){
            if($data['type'] == 'aid'){
                Db::name("{$this->getdata['models']}_content_{$_article['mid']}")->whereId($_article['id'])->update(['comment_num' => $_article['comment_num'] + 1]);
                $data['log_title'] = "评论了【{$_article['title']}】";
                event('logadd', $data);
                $_data = [
                    'title' => "你发表的 【{$_article['title']}】 有新评论",
                    'cont' => "{$this->wormuser['u_uniname']} 评论了【{$_article['title']}】: \n {$data['content']}",
                    'to_uid' => $_article['uid'],
                    'fo_uid' => '0',
                    'status' => '0',
                    'addtime' => time(),
                    'endtime' => '0',
                    '_type' => 'sms',
                ];
                event('usersms', $_data);
                if(!empty($data['pid'])){
                    $_data = $this->model->getOne($data['pid']);
                    $_data = [
                        'title' => "用户用回复了您的评论",
                        'cont' => "{$this->wormuser['u_uniname']} 回复了你在 【{$_article['title']}】的评论: \n {$data['content']}",
                        'to_uid' => $_data['uid'],
                        'fo_uid' => '0',
                        'status' => '0',
                        'addtime' => time(),
                        'endtime' => '0',
                        '_type' => 'sms',
                    ];
                    event('usersms', $_data);
                }
            } else if($data['type'] == 'uid'){
                $_text = !empty($data['money']) ? ", 增加{$data['money']}积分" : '';
                $_data = [
                    'title' => "用户对你进行了评论",
                    'cont' => "{$this->wormuser['u_uniname']} 评论了: \n {$data['content']}{$_text}。",
                    'to_uid' => $data['aid'],
                    'fo_uid' => '0',
                    'status' => '0',
                    'addtime' => time(),
                    'endtime' => '0',
                    '_type' => 'sms',
                ];
                event('usersms', $_data);
            }
            $this->result('','1','发表成功');
        }
        $this->result('','0','发表失败');
    }
}