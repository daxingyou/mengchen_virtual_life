<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use App\Models\Configuration;
use App\Models\Players;
use App\Models\StockHolders;
use App\Models\StockIpo;
use App\Models\StockIpoSbuscription;
use Illuminate\Http\Request;
use App\Rules\IpoSharesLimit;
use Illuminate\Support\Facades\DB;

class StockIpoController extends MiniProgramController
{
    public function ipo(Request $request)
    {
        $configuration = Configuration::find(1);
        $this->validate($request, [
            'stock_code' => 'required|string|max:8|unique:stock_ipo,stock_code',
            'stock_type_id' => 'required|integer|exists:stock_type,id',
            'ipo_price' => 'required|numeric',
            'ipo_shares' => ['required', 'numeric', new IpoSharesLimit($configuration)],
            'dividend_policy_id' => 'required|integer|exists:stock_dividend_policy,id',
            'intro' => 'nullable|string|max:255',
        ]);
        $data = $request->only([
            'stock_code', 'stock_type_id', 'ipo_price', 'ipo_shares', 'dividend_policy_id', 'intro',
        ]);

        $player = $this->player($request);
        throw_if(! empty($player->stock_ipo), WechatMiniProgramCommonException::class
            , '一个玩家只能发布一个ipo');

        $this->doIpo($player, $data, $configuration);
        return $this->res('操作成功');
    }

    protected function doIpo($player, $data, $configuration)
    {
        $data['issuer_id'] = $player->id;
        $data['ipo_remained_shares'] = $data['ipo_shares'];
        $data['status'] = 1;

        DB::transaction(function () use ($player, $data, $configuration) {
            //新建ipo信息
            StockIpo::create($data);

            //更新股票资产表
            StockHolders::create([
                'stock_code' => $data['stock_code'],
                'holder_id' => $player->id,
                'total_shares' => $configuration->base_ipo_shares,
                'frozen_shares' => 0,
            ]);
        });
    }

    public function getIpoInfo(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|max:8',
        ]);
        $stockCode = $request->input('stock_code');

        return StockIpo::where('stock_code', $stockCode)->firstOrFail();
    }

    public function subscription(Request $request)
    {
        $request->validate([
            'stock_code' => 'required|string|max:8',
            'subscribe_shares' => 'required|numeric',
        ]);

        $stock = StockIpo::where('stock_code', $request->input('stock_code'))->first();
        throw_if(empty($stock), WechatMiniProgramCommonException::class, '此股票不存在');

        $player = $this->player($request);
        throw_if($player->id == $stock->issuer_id, WechatMiniProgramCommonException::class,
            '不可认购自己发行的股票');

        $subscribeShares = $request->input('subscribe_shares');
        throw_if($subscribeShares > $stock->ipo_remained_shares,
            WechatMiniProgramCommonException::class, '可认购份额不足');
        throw_if($player->points < $stock->ipo_price * $subscribeShares,
            WechatMiniProgramCommonException::class, '可用身价不足以认购足量的股票');

        $this->doSubscription($player, $stock, $subscribeShares);
        return $this->res('认购成功');
    }

    protected function doSubscription($player, $stock, $subscribeShares)
    {
        DB::transaction(function () use ($player, $stock, $subscribeShares) {
            $stockCode = $stock->stock_code;

            //扣除认购这些数量的股票需要的身价
            $player->points -= $stock->ipo_price * $subscribeShares;
            $player->save();

            //增加发行者的身价
            $issuer = Players::find($stock->issuer_id);
            throw_if(empty($issuer), WechatMiniProgramCommonException::class, '发行人不存在');
            $issuer->points += $stock->ipo_price * $subscribeShares;
            $issuer->save();

            //更新股票ipo待认购数量
            $stock->ipo_remained_shares -= $subscribeShares;
            $stock->save();

            //扣除发行者的股票资产
            $issuerStockHolder = StockHolders::where('stock_code', $stockCode)
                ->where('holder_id', $stock->issuer_id)
                ->first();
            throw_if(empty($issuerStockHolder), WechatMiniProgramCommonException::class, '未找到发行者的股票资产');
            $issuerStockHolder->total_shares -= $subscribeShares;
            $issuerStockHolder->save();

            //增加认购者的股票资产
            $subscriberStockHolder = StockHolders::firstOrNew([
                'stock_code' => $stockCode,
                'holder_id' => $player->id,
            ], ['frozen_shares' => 0]);
            $subscriberStockHolder->total_shares += $subscribeShares;
            $subscriberStockHolder->save();

            //记录认购信息
            StockIpoSbuscription::create([
                'stock_code' => $stockCode,
                'subscriber' => $player->id,
                'shares_subscribed' => $subscribeShares,
            ]);
        });
    }
}
