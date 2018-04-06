<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'middleware' => ['wechat.mock', 'wechat.mauth'],
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