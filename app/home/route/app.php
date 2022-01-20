<?php
use think\facade\Route;

//Route::group(':movers/article', function () {
//    Route::rule('/', 'home/:movers.Article/index');
//    Route::rule('/index$', 'home/:movers.Article/index');
//    Route::rule('/read-<id>$', 'home/:movers.Article/index');
//})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
//
//Route::group(':movers/part', function () {
//    Route::rule('/', 'home/:movers.Part/index');
//    Route::rule('/index$', 'home/:movers.Part/index');
//    Route::rule('/read-<id>$', 'home/:movers.Part/index');
//})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);

Route::group(':movers/fupart', function () {
    Route::rule('/', 'home/:movers.Fupart/index');
    Route::rule('/index$', 'home/:movers.Fupart/index');
    Route::rule('/read-<id>$', 'home/:movers.Fupart/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);

Route::group('article', function () {
    Route::rule('/', 'home/cms.Article/index');
    Route::rule('/index$', 'home/cms.Article/index');
    Route::rule('/read-<id>$', 'home/cms.Article/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
//  专题
Route::group('special', function () {
    Route::rule('/', 'home/Special/index');
    Route::rule('/index$', 'home/Special/index');
    Route::rule('/read-<id>$', 'home/Special/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::group('special-part', function () {
    Route::rule('/', 'home/Specialclass/index');
    Route::rule('/index$', 'home/Specialclass/index');
    Route::rule('/read-<id>$', 'home/Specialclass/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);

//  订单
Route::group('paybase', function () {
    Route::rule('/read-<oid>$', 'home/Paybase/read');
})->ext('html')->pattern(['oid' => '\w+']);

Route::rule(':movers/part-<id>$', ':movers.Part/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule(':movers/article-<id>$', ':movers.Article/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule(':movers/fupart-<id>$', ':movers.Fupart/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);

Route::rule('part-<id>$', 'cms.Part/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('fupart-<id>$', 'cms.Fupart/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('article-<id>$', 'cms.Article/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('special-<id>$', 'Special/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('special-part-<id>$', 'Specialclass/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('login$', 'home/Login/login')->ext('html');
Route::rule('reg$', 'home/Login/reg')->ext('html');
Route::rule('repwd$', 'home/Login/repwd')->ext('html');

Route::rule('bbs$', 'bbs.Index/index')->ext('html');
