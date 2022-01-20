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

Route::group('index', function () {
    Route::rule('/', 'Index/index');
    Route::rule('/index$', 'Index/index');
    Route::rule('/one$', 'Index/one');
    Route::rule('/two$', 'Index/two');
    Route::rule('/three$', 'Index/three');
})->name('index')->ext('html');