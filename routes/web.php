<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

//公共接口
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('info', 'InfoController@info');
    Route::get('content-header-h1', 'InfoController@getContentHeaderH1');
});

//管理员接口
Route::group([
    'middleware' => ['auth'],
    'prefix' => 'admin/api',
    'namespace' => 'Admin'
], function () {
    Route::put('password', 'AdminController@updatePass');
    Route::get('home', 'HomeController@show');
    Route::get('system/log', 'SystemController@showLog');
});

//管理员视图路由
Route::group([
    'middleware' => ['auth'],
    'prefix' => 'admin',
    'namespace' => 'Admin'
], function () {
    Route::view('home', 'admin.home');
    Route::view('system/log', 'admin.system.log');
});

//小程序接口
Route::group([
    'middleware' => ['wechat.mock', 'wechat.mauth'],
    'prefix' => 'wechat',
    'namespace' => 'Wechat',
], function () {
    Route::post('pet/interaction', 'PetController@interact');
    Route::get('/player/info', 'PlayerController@getInfo');
    Route::put('/player/info', 'PlayerController@updateInfo');
    Route::post('points/purchase', 'PointsController@purchase');
    Route::get('stock/type', 'StockTypeController@showStockType');
    Route::post('stock/type', 'StockTypeController@addStockType');
    Route::get('stock/dividend-policy', 'StockDividendController@showDividendPolicy');
    Route::post('stock/dividend-policy', 'StockDividendController@addDividendPolicy');
    Route::delete('stock/dividend-policy', 'StockDividendController@delDividendPolicy');
    Route::post('stock/ipo', 'StockIpoController@ipo');
    Route::get('stock/ipo', 'StockIpoController@getIpoInfo');
    Route::post('stock/ipo/subscription', 'StockIpoController@subscription');
    Route::post('stock/order', 'StockTradingController@makeOrder');
    Route::delete('stock/order/{order}', 'StockTradingController@cancelOrder');
});