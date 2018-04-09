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

//微信小程序接口需要session功能来认证，web中间件不可或缺
Route::group([
    'middleware' => ['wechat.mock', 'wechat.mauth'],
    //'middleware' => ['auth:mp'],
    'prefix' => 'wechat',
    'namespace' => 'Wechat',
], function () {
    Route::post('pet/interaction', 'PetController@interact');
    Route::get('player/info', 'PlayerController@getInfo');
    Route::put('player/info', 'PlayerController@updateInfo');
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
    Route::get('stock/order/{orderId}', 'StockOrderController@getOrder')->where('orderId', '[0-9]+');
    Route::delete('stock/order/{order}', 'StockOrderController@cancelOrder');
    Route::get('stock/orders', 'StockOrderController@getPlayerOrders'); //批量获取当前玩家的订单
    Route::get('stock/orders/history', 'StockOrderController@getOrderHistory');  //获取某只股票的订单历史
    Route::get('stock/depth', 'StockMarketController@getDepth'); //获取某只股票的交易深度
    Route::get('stock/ticker', 'StockMarketController@getTicker');  //获取某只股票的最近成交价
    Route::get('stock/trend', 'StockMarketController@getTrend');    //获取所有股票的趋势（涨跌幅）
});