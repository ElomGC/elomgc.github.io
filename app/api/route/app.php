<?php

use think\facade\Route;
//  栏目管理
Route::group(':movers/part', function () {
    Route::rule('/<id?>$', 'api/:movers.Part/index');
    Route::rule('read-<id>$', 'api/:movers.Part/read');
})->ext('html')->pattern(['id' => '\d+']);
//  专题管理
Route::group(':movers/special', function () {
    Route::rule('/<id?>$', 'api/:movers.Special/index');
    Route::rule('read-<id>$', 'api/:movers.Special/read');
})->ext('html')->pattern(['id' => '\d+']);
//  专题管理
Route::group(':movers/specialclass', function () {
    Route::rule('/$', 'api/:movers.Specialclass/index');
    Route::rule('read-<id>$', 'api/:movers.Specialclass/read');
})->ext('html')->pattern(['id' => '\d+']);
//  内容管理
Route::group(':movers/article', function () {
    Route::rule('/', 'api/:movers.Article/index');
    Route::rule('read-<id>$', 'api/:movers.Article/read');
    Route::rule('create$', 'api/:movers.Article/create');
    Route::rule('save$', 'api/:movers.Article/save');
    Route::rule('household$', 'api/:movers.Article/household');
})->ext('html')->pattern(['id' => '\d+']);
//  插件管理
Route::group(':movers/coupon', function () {
    Route::rule('/', 'api/:movers.Coupon/index');
    Route::rule('read-<id>$', 'api/:movers.Coupon/read');
    Route::rule('create$', 'api/:movers.Coupon/create');
    Route::rule('save$', 'api/:movers.Coupon/save');
})->ext('html')->pattern(['id' => '\d+']);

Route::group(':movers/couponuser', function () {
    Route::rule('/', 'api/:movers.CouponUser/index');
    Route::rule('read-<cid>-<id>$', 'api/:movers.CouponUser/read');
    Route::rule('create$', 'api/:movers.CouponUser/create');
    Route::rule('save$', 'api/:movers.CouponUser/save');
})->ext('html')->pattern(['cid' => '\d+','id' => '\w+']);
//  评论管理
Route::group('comment-<models>-<type>-<aid>', function () {
    Route::rule('/', 'api/Comment/index');
    Route::rule('/save', 'api/Comment/save');
    Route::rule('read-<id>$', 'api/Comment/read');
})->ext('html')->pattern(['models' => '\w+','type' => '\w+','id' => '\d+','aid' => '\w+']);
//  点赞管理
Route::group(':movers/zan-<type>', function () {
    Route::rule('/save-<aid>', 'api/:movers.Zan/save');
})->ext('html')->pattern(['type' => '\w+','aid' => '\d+']);
//  收藏管理
Route::group(':movers/follow-<type>', function () {
    Route::rule('/', 'api/:movers.Follow/index');
    Route::rule('/save-<aid>', 'api/:movers.Follow/save');
})->ext('html')->pattern(['type' => '\w+','aid' => '\d+']);
//  订单管理
Route::group('pay', function () {
    Route::rule('/', 'api/Pay/index');
    Route::rule('/read-<oid>$', 'api/Pay/read');
})->ext('html')->pattern(['oid' => '\w+']);

Route::rule('wxbaseinfo$', 'api/WxServer/baseinfo')->ext('html');
Route::rule('wxpayend$', 'api/PayBase/wxpayend')->ext('html');
Route::rule('wxpay-<oid>$', 'api/Pay/payWx')->ext('html')->pattern(['oid' => '\w+']);

