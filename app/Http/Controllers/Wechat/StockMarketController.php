<?php

namespace App\Http\Controllers\Wechat;

use App\Models\StockTradingHistory;
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
}
