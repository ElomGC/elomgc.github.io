<?php
return [
    'view_dir_name' => 'default',
    'view_depr'     => '_',
    //  定义模板目录
    'view_path'    => 'view/install',
    //  定义公用资源目录
    'tpl_replace_string' => [
        '__IMAGES__'=> '/public/install',
        '__PUBLIC__'=> '/public/wormcms',
        '__LAYUI__'=> '/public/wormcms/layui',
        '__CKEDITOR__'=> '/public/wormcms/ckeditor',
    ],
];