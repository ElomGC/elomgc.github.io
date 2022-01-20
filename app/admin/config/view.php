<?php
return [
    'view_dir_name' => 'default',
    //  定义模板目录
    'view_path'    => 'view/admin',
    'view_default_dir'    => 'default',
    //  定义公用资源目录
    'tpl_replace_string' => [
        '__IMAGES__'=> '/public/admin',
        '__PUBLIC__'=> '/public/wormcms',
        '__LAYUI__'=> '/public/wormcms/layui',
        '__CKEDITOR__'=> '/public/wormcms/ckeditor',
    ],
];