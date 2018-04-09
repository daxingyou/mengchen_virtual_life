<?php

namespace App\Services;

use App\Models\Players;
use Illuminate\Http\Request;

class WechatMiniProgramService
{
    /**
     * @param Request $request
     * @return \App\Models\Players
     */
    public static function getPlayer(Request $request, $wechatUserInfo = null)
    {
        $wechatUserInfo = is_null($wechatUserInfo) ? session($request->input('auth_code')) : $wechatUserInfo;
        $player = Players::where('openid', $wechatUserInfo->openid)->first();
        if (empty($player)) {
            Players::create([
                'openid' => $wechatUserInfo->openid,
            ]);     //直接返回创建的模型，新用户的话，返回的http代码是201
            $player = Players::where('openid', $wechatUserInfo->openid)->first();
        }
        return $player;
    }
}