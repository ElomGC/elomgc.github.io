<?php
declare(strict_types = 1);

namespace app\common\controller;

use think\facade\View;

class MemberBase extends Base
{
    protected $model;
    protected $validate;
    protected $list_file;
    protected $form_list;
    protected $list_nav;
    protected $list_top;
    protected $list_search;
    protected $list_rightbtn;
    protected $list_temp = true;
    protected $list_base = [
        'id' => 'id',
        'add' => true,
        'edit' => true,
        'del' => true,
        'list_edit' => true,
        'list_switch' => true,
    ];
    protected $list_page = true;

    protected function initialize()
    {
        parent::initialize();
        if(empty($this->wormuser)){
            $this->redirect(url('home/login/login'));
        }
        $this->tempview();
        $NavList = $this->getNav();
        $LinkList = $this->getLink();
        View::assign(['NavList' => $NavList,'LinkList' => $LinkList]);
    }
}