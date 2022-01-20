<?php
declare(strict_types = 1);

namespace app\admin\controller\cms;
use app\common\controller\admin\Conf;
use app\facade\hook\Common;

class Config extends Conf {

    protected function getConf()
    {
        parent::getConf();
        $this->form_list = [];
        foreach ($this->conflist as $k => $v) {
            if($v['conf'] == $this->basename.'_order'){
                continue;
            }
            $this->form_list[] = Common::ReadFile($v);
        }
    }
}