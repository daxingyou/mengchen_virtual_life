<?php

namespace App\Services;

use App\Models\Players;
use Illuminate\Http\Request;
use Overtrue\Socialite\User;
use GuzzleHttp\Client;

class WechatMiniProgramService
{
    /**
     * @return \App\Models\Players
     */
    public static function getPlayer($request = null)
    {
        $sessionKey = 'wechat.oauth_user.default';
        $userInfo = session($sessionKey);   //通过了oauth中间件的session不会为空

        $openId = $userInfo->getId();
        $player = Players::where('openid', $openId)->first();
        if (empty($player)) {
            $player = self::createPlayer($userInfo);
        }
        return $player;
    }

    /**
     * @param \Overtrue\Socialite\User $user
     * @return \App\Models\Players
     */
    protected static function createPlayer(User $user)
    {
        $userInfo = $user->getOriginal();
        Players::create([
            'openid' => $userInfo['openid'],
            'nickname' => $userInfo['nickname'],
            'gender' => $userInfo['sex'],
        ]);     //直接返回创建的模型，新用户的话，返回的http代码是201，所以重新从数据库拿一遍
        $player = Players::where('openid', $userInfo['openid'])->first();

        //下载图片到本地
        $subDir = 'avatar';
        $avatarUrl = config('filesystems.disks.wechat.root') . '/' . $subDir . '/' . $player->id;
        $httpClient = new Client([
            'verify' => false,
        ]);
        $httpClient->get($userInfo['headimgurl'], [
            'sink' => $avatarUrl,
        ]);

        return $player;
    }
}