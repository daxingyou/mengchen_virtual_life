<?php

namespace App\Http\Controllers\Wechat;

use App\Models\StockClosingPrice;
use App\Models\StockTradingHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StockOrders;

class StockMarketController extends MiniProgramController
{
    /**
     *
     * @SWG\Get(
     *     path="/stock/depth",
     *     description="获取某只股票的交易深度",
     *     operationId="stock.depth.get",
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
     *         description="返回此股票的交易深度",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/StockDepth"),
     *             }
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
                        $totalRemainedShares = sprintf('%.8f', $item->sum('remained_shares'));
                        array_push($result, [$price, $totalRemainedShares]);
                    });
                return $result;
            });
    }

    /**
     *
     * @SWG\Get(
     *     path="/stock/ticker",
     *     description="获取某只股票的最新成交数据",
     *     operationId="stock.ticker.get",
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
     *         description="返回此股票的ticker",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/StockTicker"),
     *             }
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

    /**
     *
     * @SWG\Get(
     *     path="/stock/trend",
     *     description="获取所有(指定)股票的趋势变化",
     *     operationId="stock.trend.get",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="stock_code",
     *         description="股票代码",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回此股票的trend",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/StockTrend"),
     *             }
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
            ->map(function ($item, $stockCode) {
                $owner = $item->first()->owner;
                $lastPrice = $item->sortByDesc('id')->first()->price;
                $yesterdayClosingPrice = $this->getYesterdayClosingPrice($stockCode); //昨日收盘价
                $todayLastPrice = $this->getTodayLastPrice($item);
                $changingRate = ($yesterdayClosingPrice === 0 or $todayLastPrice === 0) ? 0
                    : sprintf('%.4f', ($todayLastPrice - $yesterdayClosingPrice) / $yesterdayClosingPrice);
                return [
                    'changing_rate' => $changingRate,
                    'last_price' => $lastPrice,             //最新成交价（可能是多天之前的）
                    'today_last_price' => $todayLastPrice,  //今日最新成交价，如果没成交就为0
                    'owner' => $owner,
                ];
            });
        return $stockTradingHistory;
    }

    protected function getYesterdayClosingPrice($stockCode)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $yesterdayStockPrice = StockClosingPrice::where('stock_code', $stockCode)
            ->whereDate('date', $yesterday)
            ->first();
        return empty($yesterdayStockPrice) ? 0 : $yesterdayStockPrice->closing_price;
    }

    protected function getTodayLastPrice($item)
    {
        $item = $item->filter(function ($value) {
            return Carbon::parse($value->created_at)->isToday();
        });

        return $item->isEmpty() ? 0 : $item->sortByDesc('id')->first()->price;
    }

    /**
     *
     * @SWG\Get(
     *     path="/stock/kline",
     *     description="获取指定股票的kline",
     *     operationId="stock.kline.get",
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
     *         description="返回此股票的kline",
     *         @SWG\Property(
     *             type="array",
     *             example={"9.00000000", "2.00000000", "2.00000000", "2.00000000", "2.00000000"},
     *             @SWG\Items(
     *                 type="string",
     *                 minItems=5,
     *                 maxItems=5,
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
    public function getKline(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|exists:stock_holders,stock_code',
        ]);
        $closingPriceAmount = 4;    //收盘价最近4天的
        $stockClosingPrice = array_reverse(StockClosingPrice::where('stock_code', $request->stock_code)
            ->orderBy('id', 'desc')
            ->limit($closingPriceAmount)
            ->get()
            ->pluck('closing_price')
            ->toArray());
        $tradingHistory = StockTradingHistory::where('stock_code', $request->stock_code)
            ->orderBy('id', 'desc')
            ->first();
        $lastPrice = empty($tradingHistory) ? sprintf('%.8f', 0) : $tradingHistory->price;
        array_push($stockClosingPrice, $lastPrice);
        return $stockClosingPrice;
    }
}
