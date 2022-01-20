<?php
declare(strict_types = 1);
namespace app\api\controller\quan;

use app\common\controller\ApiBase;
use app\common\wormview\AddEditList;

class Coupon extends ApiBase
{
    use AddEditList;
    protected $basename;
    protected $UserModel;
    protected $list_temp = false;
    protected function initialize()
    {
        parent::initialize();
        //  定义模型
        preg_match_all('/([_a-z]+)/', toUnderScore(get_called_class()), $array);
        $this->basename = $array['0']['3'];
        $mdodel = "app\\common\\model\\" . $this->basename . "\\Coupon";
        $this->model = new $mdodel;
        $mdodel = "app\\common\\model\\" . $this->basename . "\\CouponUser";
        $this->UserModel = new $mdodel;
        $this->validate = "app\\common\\validate\\Coupon.php";
    }
}