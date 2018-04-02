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
    public function getOrder(Request $request, $orderId)
    {
        return StockOrders::find($orderId);
    }

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

    public function getDepth(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|exists:stock_holders,stock_code',
        ]);

        return StockOrders::whereIn('status', [1, 2])
            ->get()
            ->groupBy('direction')
            ->map(function ($item) {
                $result = [];
                $item->sortBy('price')->groupBy('price')
                    ->each(function ($item, $price) use (&$result) {
                        array_push($result, [$price, $item->sum('remained_shares')]);
                    });
                return $result;
            });
    }

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
