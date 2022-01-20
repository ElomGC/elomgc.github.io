<?php
use think\facade\Route;

Route::group('article', function () {
    Route::rule('/', 'Article/index');
    Route::rule('/index$', 'Article/index');
    Route::rule('/read$', 'Article/index');
    Route::rule('/read-<id>$', 'Article/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::group('part', function () {
    Route::rule('/', 'Part/index');
    Route::rule('/index$', 'Part/index');
    Route::rule('/read-<id>$', 'Part/index');
})->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);

Route::rule('part-<id>$', 'Part/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('fupart-<id>$', 'Fupart/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
Route::rule('article-<id>$', 'Article/index')->ext('html')->append(['status' => 1])->pattern(['id' => '\d+']);
