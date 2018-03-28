<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use App\Models\StockIpo;
use Illuminate\Http\Request;
use App\Rules\IpoSharesLimit;

class StockIpoController extends MiniProgramController
{
    public function doIpo(Request $request)
    {
        $this->validate($request, [
            'stock_code' => 'required|string|max:8',
            'stock_type_id' => 'required|integer|exists:stock_type,id',
            'ipo_price' => 'required|numeric',
            'ipo_shares' => ['required', 'numeric', new IpoSharesLimit(1)],
            'dividend_policy_id' => 'required|integer|exists:stock_dividend_policy,id',
            'intro' => 'nullable|string|max:255',
        ]);
        $data = $request->only([
            'stock_code', 'stock_type_id', 'ipo_price', 'ipo_shares', 'dividend_policy_id', 'intro',
        ]);

        $player = $this->player($request);
        throw_if(! empty($player->stock_ipo), WechatMiniProgramCommonException::class
            , '一个玩家只能发布一个ipo');

        $data['issuer_id'] = $player->id;
        $data['ipo_remained_shares'] = $data['ipo_shares'];
        $data['status'] = 1;

        StockIpo::create($data);

        return $this->res('操作成功');
    }
}
