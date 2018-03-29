<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use Illuminate\Http\Request;

class StockTradingController extends MiniProgramController
{
    public function makeOrder(Request $request)
    {
        $request->validate([
            'direction' => 'required|string|in:bid,ask',
            'stock_code' => 'required|string|max:8|exists:stock_ipo,stock_code',
            'price' => 'required|numeric',
            'shares' => 'required|numeric',
        ]);
        $data = $request->only([
            'direction', 'stock_code', 'price', 'shares',
        ]);
        $player = $this->player($request);

        throw_if($player->hasEnoughAvailableShares($data['stock_code'], $data['shares']),
            WechatMiniProgramCommonException::class, '可用股份不足');

        $this->doMakeOrder($player, $data);
        return $this->res('下单成功');
    }

    protected function doMakeOrder($player, $data)
    {
        //
    }
}
