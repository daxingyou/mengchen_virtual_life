<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use App\Models\Players;
use App\Models\StockOrders;
use App\Models\StockTradingHistory;
use Illuminate\Http\Request;
use App\Models\StockIpo;
use Illuminate\Support\Facades\DB;

class StockTradingController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *     path="/stock/order",
     *     description="下单",
     *     operationId="stock.order.add",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="direction",
     *         description="下单方向（buy or sell）",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="stock_code",
     *         description="股票代码",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         description="下单价格",
     *         in="query",
     *         required=true,
     *         type="number",
     *     ),
     *     @SWG\Parameter(
     *         name="shares",
     *         description="下单数量",
     *         in="query",
     *         required=true,
     *         type="number",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="下单成功",
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
     *     @SWG\Response(
     *         response=422,
     *         description="请求参数验证失败",
     *         @SWG\Schema(
     *             ref="#/definitions/ValidationError",
     *         ),
     *     ),
     * )
     */
    public function makeOrder(Request $request)
    {
        $request->validate([
            'direction' => 'required|string|in:buy,sell',
            'stock_code' => 'required|string|max:8|exists:stock_ipo,stock_code',
            'price' => 'required|numeric',
            'shares' => 'required|numeric',
        ]);
        $data = $request->only([
            'direction', 'stock_code', 'price', 'shares',
        ]);
        $player = $this->player($request);

        //检查这支股票IPO是否完成
        throw_if(! $this->ifIpoFinished($data['stock_code']),
            WechatMiniProgramCommonException::class, '此股票ipo未完成，禁止交易');

        throw_if(! $this->hasEnoughAsset($player, $data),
            WechatMiniProgramCommonException::class, '可用资产不足');

        $this->doMakeOrder($player, $data);
        return $this->res('下单成功');
    }

    protected function doMakeOrder(Players $player, Array $data)
    {
        DB::transaction(function () use ($player, $data) {
            $this->frozeAsset($player, $data);
            $order = $this->placeOrder($player, $data);
            $this->doOrderMatch($order);
        });
    }

    protected function ifIpoFinished($stockCode)
    {
        $stockUnderIpo = StockIpo::where('stock_code', $stockCode)->first();
        return bccomp($stockUnderIpo->ipo_remained_shares, 0, 8) === 0;
    }

    protected function hasEnoughAsset(Players $player, Array $data)
    {
        switch ($data['direction']) {
            //买单，比较可用身价大于或等于下单总身价
            case 'buy':
                return bccomp($player->points, $data['price'] * $data['shares'], 8) !== -1;
                break;
            //卖单，比较可用股票数
            case 'sell':
                return $player->hasEnoughAvailableShares($data['stock_code'], $data['shares']);
                break;
            default:
                throw new WechatMiniProgramCommonException('未知的direction');
        }
    }

    protected function frozeAsset(Players $player, Array $data)
    {
        switch ($data['direction']) {
            //买单，冻结身价
            case 'buy':
                $frozePoints = $data['price'] * $data['shares'];
                $player->points -= $frozePoints;
                $player->frozen_points += $frozePoints;
                $player->save();
                break;
            //卖单，冻结股票
            case 'sell':
                $stockHolder = $player->stock($data['stock_code']);
                $stockHolder->total_shares -= $data['shares'];
                $stockHolder->frozen_shares += $data['shares'];
                $stockHolder->save();
                break;
            default:
                throw new WechatMiniProgramCommonException('未知的direction');
        }
    }

    /**
     * @param Players $player
     * @param array $data
     * @return \App\Models\StockOrders
     */
    protected function placeOrder(Players $player, Array $data)
    {
        return StockOrders::create([
            'stock_code' => $data['stock_code'],
            'player_id' => $player->id,
            'direction' => $data['direction'],
            'price' => $data['price'],
            'shares' => $data['shares'],
            'remained_shares' => $data['shares'],   //新订单，待成交等于下单数量
            'avg_price' => 0,
            'status' => 1,
        ]);
    }

    protected function doOrderMatch(StockOrders $order)
    {
        //如果此taker订单已经完全成交，直接返回
        if ($order->status === 3) {
            return true;
        }

        //获取对面方向的买1或卖1单
        $firstOppositeOrder = $this->getFirstOppositeOrder($order);
        if (is_null($firstOppositeOrder)) {
            return true;    //对面没有下单深度时直接返回
        }

        if (! $this->matchPrice($order, $firstOppositeOrder)) {
            return true;    //如果价格不匹配，即无法成交，直接返回
        }

        //throw new WechatMiniProgramCommonException('price match');
        $this->doTrading($order, $firstOppositeOrder);  //找到匹配订单后，执行交易
    }

    protected function matchPrice($takerOrder, $makerOrder)
    {
        switch ($takerOrder->direction) {
            case 'buy':
                return bccomp($takerOrder->price, $makerOrder->price, 8) !== -1;
                break;
            case 'sell':
                return bccomp($makerOrder->price, $takerOrder->price, 8) !== -1;
                break;
            default:
                throw new WechatMiniProgramCommonException('未知的direction');
        }
    }

    protected function doTrading($takerOrder, $makerOrder)
    {
        $dealPrice = $makerOrder->price;
        $dealShares = $this->getDealShares($takerOrder, $makerOrder);

        $this->updateOrder($takerOrder, $makerOrder, $dealPrice, $dealShares);          //更新订单状态
        $this->transferAsset($takerOrder, $makerOrder, $dealPrice, $dealShares);        //转移资产
        $this->returnTakerExcessFrozenAsset($takerOrder);       //返回吃单者冻结的多余的资产（成交价小于下单价）
        $this->logTradingHistory($takerOrder, $makerOrder, $dealPrice, $dealShares);    //记录订单历史
        $this->doOrderMatch($takerOrder);   //递归匹配逻辑，继续下一个交易轮询
    }

    protected function updateOrder($takerOrder, $makerOrder, $dealPrice, $dealShares)
    {
        //平均成交价应该先于remained_shares计算，不然数据有问题
        $takerOrder->updateAvgPrice($dealPrice, $dealShares);
        $takerOrder->remained_shares -= $dealShares;
        $takerOrder->updateStatus();
        $takerOrder->save();

        $makerOrder->updateAvgPrice($dealPrice, $dealShares);
        $makerOrder->remained_shares -= $dealShares;
        $makerOrder->updateStatus();
        $makerOrder->save();
    }

    protected function getDealShares($takerOrder, $makerOrder)
    {
        //taker为成交数大于maker未成交数
        if (bccomp($takerOrder->remained_shares, $makerOrder->remained_shares, 8) === 1) {
            $dealShares = $makerOrder->remained_shares;
        } elseif (bccomp($takerOrder->remained_shares, $makerOrder->remained_shares, 8) === 0) {
            $dealShares = $makerOrder->remained_shares;
        } else {    //taker未成交数小于maker未成交数
            $dealShares = $takerOrder->remained_shares;
        }
        return $dealShares;
    }

    protected function transferAsset($takerOrder, $makerOrder, $dealPrice, $dealShares)
    {
        $taker = $takerOrder->getPlayer();
        $maker = $makerOrder->getPlayer();
        $takerStockHolder = $takerOrder->getStockHolder();
        $makerStockHolder = $makerOrder->getStockHolder();
        $totalPrice = $dealPrice * $dealShares;
        switch ($takerOrder->direction) {
            case 'buy':
                //如果taker是买单，那么其冻结的就是身价
                $taker->frozen_points -= $totalPrice;
                $maker->points += $totalPrice;

                $makerStockHolder->frozen_shares -= $dealShares;
                $takerStockHolder->total_shares += $dealShares;
                break;
            case 'sell':
                //如果taker是卖单，那么其冻结的就是股票
                $takerStockHolder->frozen_shares -= $dealShares;
                $makerStockHolder->total_shares += $dealShares;

                $maker->frozen_points -= $totalPrice;
                $taker->points += $totalPrice;
                break;
            default:
                throw new WechatMiniProgramCommonException('未知的direction');
        }

        $taker->save();
        $maker->save();
        $takerStockHolder->save();
        $makerStockHolder->save();
    }

    protected function returnTakerExcessFrozenAsset($takerOrder)
    {
        //如果吃单者下单方向是卖，那么冻结的是股票，不会存在多余的冻结资产
        if ($takerOrder->direction === 'sell') {
            return true;
        }

        //所有下单的股票都已成交之后才返还多余的冻结资产
        if ($takerOrder->status !== 3) {
            return true;
        }

        //下单价和平均成交价相等，刚好成交，无需返回
        if ((bccomp($takerOrder->price, $takerOrder->avg_price, 8) === 0)) {
            return true;
        }

        $taker = $takerOrder->getPlayer();
        $takerFrozenPoints = $takerOrder->price * $takerOrder->shares;
        $totalDealPoints = $takerOrder->avg_price * $takerOrder->shares;
        $taker->frozen_points -= $takerFrozenPoints - $totalDealPoints;
        $taker->points += $takerFrozenPoints - $totalDealPoints;
        $taker->save();
    }

    protected function logTradingHistory($takerOrder, $makerOrder, $dealPrice, $dealShares)
    {
        return StockTradingHistory::create([
            'stock_code' => $takerOrder->stock_code,
            'price' => $dealPrice,
            'shares' => $dealShares,
            'taker_direction' => $takerOrder->direction,
            'maker_order_id' => $makerOrder->id,
            'taker_order_id' => $takerOrder->id,
        ]);
    }

    protected function getFirstOppositeOrder(StockOrders $order)
    {
        switch ($order->direction) {
            case 'buy':
                return StockOrders::where('direction', 'sell')
                    ->whereIn('status', [1,2])
                    ->orderBy('price')
                    ->first();
                break;
            case 'sell':
                return StockOrders::where('direction', 'buy')
                    ->whereIn('status', [1,2])
                    ->orderBy('price')
                    ->first();
                break;
            default:
                throw new WechatMiniProgramCommonException('未知的direction');
        }
    }
}