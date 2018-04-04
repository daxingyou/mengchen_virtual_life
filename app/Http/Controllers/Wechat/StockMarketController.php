<?php

namespace App\Http\Controllers\Wechat;

use App\Models\StockTradingHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StockOrders;

class StockMarketController extends MiniProgramController
{
    public function getDepth(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|exists:stock_holders,stock_code',
        ]);

        return StockOrders::whereIn('status', [1, 2])
            ->where('stock_code', $request->stock_code)
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

    public function getTicker(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|exists:stock_holders,stock_code',
        ]);

        return StockTradingHistory::where('stock_code', $request->stock_code)
            ->orderBy('id', 'desc')
            ->firstOrFail()
            ->setHidden(['id', 'maker_order_id', 'taker_order_id']);
    }

    public function getTrend(Request $request)
    {
        $request->validate([
            'stock_code' => 'nullable|string|exists:stock_holders,stock_code',
        ]);

        $stockTradingHistory = StockTradingHistory::when($request->has('stock_code'), function ($qurey) use ($request) {
            $qurey->where('stock_code', $request->stock_code);
        })
            ->get()
            ->each(function ($item) {
                $item->append('owner');
            })
            ->groupBy('stock_code')
            ->map(function ($item) {
                $owner = $item->first()->owner;
                $lastPrice = $item->sortByDesc('id')->first()->price;
                $yesterdayClosingPrice = $this->getYesterdayClosingPrice($item); //昨日收盘价
                $todayLastPrice = $this->getTodayLastPrice($item);
                $changingRate = ($yesterdayClosingPrice === 0 or $todayLastPrice === 0) ? 0
                    : sprintf('%.4f', ($todayLastPrice - $yesterdayClosingPrice) / $yesterdayClosingPrice);
                return [
                    'changing_rate' => $changingRate,
                    'last_price' => $lastPrice,
                    'today_last_price' => $todayLastPrice,
                    'owner' => $owner,
                ];
            });
        return $stockTradingHistory;
    }

    protected function getYesterdayClosingPrice($item)
    {
        $item = $item->filter(function ($value) {
            return Carbon::parse($value->created_at)->isYesterday();
        });

        return $item->isEmpty() ? 0 : $item->sortByDesc('id')->first()->price;
    }

    protected function getTodayLastPrice($item)
    {
        $item = $item->filter(function ($value) {
            return Carbon::parse($value->created_at)->isToday();
        });

        return $item->isEmpty() ? 0 : $item->sortByDesc('id')->first()->price;
    }
}
