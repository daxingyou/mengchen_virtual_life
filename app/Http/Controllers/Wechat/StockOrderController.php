<?php

namespace App\Http\Controllers\Wechat;

use App\Models\Players;
use App\Models\StockTradingHistory;
use Illuminate\Http\Request;
use App\Models\StockOrders;
use App\Exceptions\WechatMiniProgramCommonException;
use Illuminate\Support\Facades\DB;

class StockOrderController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return \App\Models\StockOrders
     *
     * @SWG\Get(
     *     path="/stock/order/{orderId}",
     *     description="获取单个订单信息",
     *     operationId="stock.order.get",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="orderId",
     *         description="订单id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回订单信息",
     *         @SWG\Schema(
     *             ref="#/definitions/StockOrder",
     *         ),
     *     ),
     * )
     */
    public function getOrder(Request $request, $orderId)
    {
        return StockOrders::find($orderId);
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Get(
     *     path="/stock/orders",
     *     description="批量获取当前玩家的订单",
     *     operationId="stock.orders.get",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="stock_code",
     *         description="股票代码",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         description="订单状态码",
     *         in="query",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="direction",
     *         description="订单方向（buy or sell）",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回订单信息",
     *         @SWG\Property(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 allOf={
     *                     @SWG\Schema(ref="#/definitions/StockOrder"),
     *                 }
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="请求参数验证失败",
     *         @SWG\Schema(
     *             ref="#/definitions/ValidationError",
     *         ),
     *     ),
     * )
     */
    public function getPlayerOrders(Request $request)
    {
        $request->validate([
            'stock_code' => 'nullable|string|exists:stock_holders,stock_code',
            'status' => 'nullable|integer|in:1,2,3,4',
            'direction' => 'nullable|string|in:buy,sell',
        ]);
        $player = $this->player($request);

        return StockOrders::where('player_id', $player->id)
            ->when($request->has('stock_code'), function ($query) use ($request) {
                $query->where('stock_code', $request->stock_code);
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('direction'), function ($query) use ($request) {
                $query->where('direction', $request->direction);
            })
            ->get();
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Get(
     *     path="/stock/orders/history",
     *     description="获取某只股票的订单历史",
     *     operationId="stock.order.history.get",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="stock_code",
     *         description="股票代码",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回订单历史",
     *         @SWG\Property(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 allOf={
     *                     @SWG\Schema(ref="#/definitions/StockTradingHistory"),
     *                 }
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="请求参数验证失败",
     *         @SWG\Schema(
     *             ref="#/definitions/ValidationError",
     *         ),
     *     ),
     * )
     */
    public function getOrderHistory(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|exists:stock_holders,stock_code',
        ]);
        return StockTradingHistory::where('stock_code', $request->stock_code)
            ->get()
            ->each(function ($item) {
                $item->setHidden(['maker_order_id', 'taker_order_id']);
            });
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Delete(
     *     path="/stock/order/{order}",
     *     description="取消订单",
     *     operationId="stock.order.del",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="order",
     *         description="订单id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="取消成功",
     *         @SWG\Schema(
     *             ref="#/definitions/Success",
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="通用错误",
     *         @SWG\Schema(
     *             ref="#/definitions/CommonError",
     *         ),
     *     ),
     * )
     */
    public function cancelOrder(Request $request, StockOrders $order)
    {
        if (in_array($order->status, [3, 4])) {
            throw new WechatMiniProgramCommonException('订单已成交或已取消');
        }

        $player = $this->player($request);
        if ($order->player_id !== $player->id) {
            throw new WechatMiniProgramCommonException('禁止取消别人的订单');
        }

        DB::transaction(function () use ($order, $player) {
            //返还冻结资产
            switch ($order->direction) {
                case 'buy':
                    $points = $order->total_price - ($order->avg_price * $order->deal_shares);
                    $player->frozen_points -= $points;
                    $player->points += $points;
                    $player->save();
                    break;
                case 'sell':
                    $shares = $order->remained_shares;
                    $stockHolder = $player->stock($order->stock_code);
                    $stockHolder->frozen_shares -= $shares;
                    $stockHolder->total_shares += $shares;
                    $stockHolder->save();
                    break;
                default:
                    throw new WechatMiniProgramCommonException('未知的direction');
            }

            //更改订单状态
            $order->status = 4;
            $order->save();
        });

        return $this->res('取消成功');
    }
}
