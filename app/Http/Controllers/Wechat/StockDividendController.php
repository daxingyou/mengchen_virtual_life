<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use App\Models\StockDividendPolicy;
use Illuminate\Http\Request;

class StockDividendController extends MiniProgramController
{
    public function showDividendPolicy(Request $request)
    {
        return StockDividendPolicy::all();
    }

    public function addDividendPolicy(Request $request)
    {
        $this->validate($request, [
            'policy' => 'required|string|max:255',
        ]);

        $player = $this->player($request);
        StockDividendPolicy::create([
            'policy' => $request->input('policy'),
            'player_id' => $player->id,
        ]);

        return $this->res('操作成功');
    }

    public function delDividendPolicy(Request $request)
    {
        $policy = StockDividendPolicy::find($request->input('policy_id'));
        throw_if(empty($policy), WechatMiniProgramCommonException::class, 'policy不存在');

        $player = $this->player($request);
        throw_if($policy->player_id !== $player->id, WechatMiniProgramCommonException::class
            , '只能操作您自己创建的policy');

        $policy->delete();
        return $this->res('操作成功');
    }
}
