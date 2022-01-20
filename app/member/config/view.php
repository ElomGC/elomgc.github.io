<?php
return [
    'view_dir_name' => 'default',
    //  定义模板目录
    'view_path'    => 'view/member',
    //  定义公用资源目录
    'tpl_replace_string' => [
        '__IMAGES__'=> '/public/member',
        '__PUBLIC__'=> '/public/wormcms',
        '__LAYUI__'=> '/public/wormcms/layui',
        '__CKEDITOR__'=> '/public/wormcms/ckeditor',
    ],
];