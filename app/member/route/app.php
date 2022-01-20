<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
//  内容管理
Route::group(':movers/article', function () {
    Route::rule('/', 'member/:movers.Article/index');
    Route::rule('read-<id>$', 'member/:movers.Article/read');
    Route::rule('create$', 'member/:movers.Article/create');
    Route::rule('save$', 'member/:movers.Article/save');
})->ext('html')->pattern(['id' => '\d+']);

Route::group(':movers/follow-<type>', function () {
    Route::rule('/', 'member/:movers.Follow/index');
    Route::rule('/save-<aid>', 'member/:movers.Follow/save');
})->ext('html')->pattern(['type' => '\w+','aid' => '\d+']);