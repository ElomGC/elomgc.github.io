<?php
declare(strict_types = 1);
namespace app\api\controller\quan;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;

class CouponUser extends ApiBase {
    use AddEditList;
    protected $basename;
    protected $CoupModel;
    protected $list_temp = false;
    protected function initialize()
    {
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\" . $this->basename . "\\CouponUser";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\\Coupon";
        $this->CoupModel = new $mdodel;
        $this->validate = "app\\common\\validate\\Coupon.php";
        if(!$this->isLogin()){
            $this->result('','10000','请登录');
        }
    }
    protected function getMap()
    {
        $map = [
            'uid' => $this->wormuser['uid'],
            'cid' => empty($this->getdata['cid']) ? '' : $this->getdata['cid'],
            'id' => empty($this->getdata['id']) ? '' : $this->getdata['id'],
        ];
        return $map; // TODO: Change the autogenerated stub
    }

    public function read($cid,$id)
    {
        $getlist = $this->model->getList(['cid' => $cid,'id' => $id,'uid' => $this->wormuser['uid']]);
        if($getlist['total'] < '1'){
            $this->result('','0','优惠券不存在');
        }
        $getlist = $getlist['data']['0'];
        return $this->viewApiRead($getlist);
    }

    public function save()
    {
        if(!$this->request->isPost()){
            $this->result('','0','非法访问');
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['cid']) && empty($data['id'])){
            $this->result('','0','非法访问');
        }
        if(!empty($data['cid'])) {
            $_old = $this->model->getList(['cid' => $data['cid'],'uid' => $this->wormuser['uid']]);
            //  查询是否有未使用的
            if($_old['total'] > '0'){
                $this->result('','0','您已经领取过了');
            }
            $_coupon = $this->CoupModel->getOne($data['cid']);
            if(empty($_coupon)){
                $this->result('','0','此优惠券不存在');
            }
            if($_coupon['time_type'] == '0' && strtotime($_coupon['end_time']) < time()){
                $this->result('','0','此优惠券已过期');
            }
            if($_coupon['zonenum'] > '0'){
                $_old = $this->model->getList(['cid' => $data['cid'],'status' => 'a']);
                if($_coupon['zonenum'] <= $_old['total']){
                    $this->result('','0','已经领完了');
                }
            }
            if($_coupon['onelimit'] > '0'){
                $_old = $this->model->getList(['cid' => $data['cid'],'status' => 'a','uid' => $this->wormuser['uid']]);
                if($_coupon['onelimit'] <= $_old['total']){
                    $this->result('','0',"每人只能领取 {$_coupon['onelimit']} 张哦");
                }
            }
            $_coupon = $this->CoupModel->generate($_coupon);
            $_coupon['uid'] = $this->wormuser['uid'];
            $data = $_coupon;
        }
        $_data = $this->model->getEditAdd($data);
        $_data['id'] = $data['id'];
        if ($add = $this->model->setOne($_data)){
            $this->result($add,'1','优惠券领取成功');
        }
        $this->result('','0','优惠券领取失败');
    }
}