<?php

namespace App\Services;

use App\Models\Players;
use Illuminate\Http\Request;

class WechatMiniProgramService
{
    public static function getPlayer(Request $request)
    {
        $wechatUserInfo = session($request->input('auth_code'));
        $player = Players::where('openid', $wechatUserInfo->openid)->first();
        if (empty($player)) {
            return Players::create([
                'openid' => $wechatUserInfo->openid,
            ]);
        } else {
            return $player;
        }
    }
}