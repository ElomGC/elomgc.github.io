<?php
declare(strict_types = 1);

namespace app\common\model\shop;


use app\common\model\PartBase;
class Part extends PartBase {
    protected $schema = [
        "id" => "int",
        "title" => "string",
        "class" => "tinyint",
        "pid" => "int",
        "mid" => "int",
        "limit" => "int",
        "title_num" => "int",
        "cont_num" => "int",
        "group_uid" => "string",
        "group_see" => "string",
        "group_edit" => "string",
        "pid_see" => "tinyint",
        "comment_see" => "tinyint",
        "status" => "tinyint",
        "jumpurl" => "string",
        "keywords" => "string",
        "description" => "mediumtext",
        "chinalist" => "mediumtext",
        "password" => "string",
        "temp_late" => "string",
        "temp_head" => "string",
        "temp_list" => "string",
        "temp_cont" => "string",
        "temp_foot" => "string",
        'sort'      => 'int',
        'addtime'      => 'int',
        'del_time'      => 'int',
        "logo" => "string",
        "banber" => "string",
        "order" => "string",
        "order_level" => "int",
    ];
}