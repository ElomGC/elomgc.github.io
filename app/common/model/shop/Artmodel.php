<?php
declare(strict_types = 1);

namespace app\common\model\shop;

use app\common\model\ModelBase;
class Artmodel extends ModelBase{
    protected $schema = [
        'id'          => 'int',
        'title'        => 'string',
        'futitle'        => 'string',
        'see_group'        => 'string',
        'edit_group'        => 'string',
        'status'      => 'tinyint',
        'see_picurl'      => 'tinyint',
        'see_keyword'      => 'tinyint',
        'see_description'      => 'tinyint',
        'see_comment'      => 'tinyint',
        'see_add'      => 'tinyint',
        'order'      => 'tinyint',
        'order_group'      => 'tinyint',
        'order_group_level'      => 'tinyint',
        'chinalist'      => 'text',
        'order_money'      => 'varchar',
        'addtime'      => 'int',
        'del_time'      => 'int',
        'sort'      => 'int',
    ];
}