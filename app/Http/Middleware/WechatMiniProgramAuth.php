<?php

namespace App\Http\Middleware;

use App\Exceptions\WechatMiniProgramAuthException;
use Carbon\Carbon;
use Closure;
use EasyWeChat;

class WechatMiniProgramAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //登录流程
        if ($request->has('js_code')) {
            $wechatApp = EasyWeChat::miniProgram();
            $jsCode = $request->input('js_code');
            //通过js_code请求微信服务器，获取用户信息
            $userInfo = $wechatApp->auth->session($jsCode);
            $authCode = $this->generateAuthCode($userInfo->openid, Carbon::now()->timestamp);
            //将用户信息存储在session中，session key为authCode
            session([$authCode => $userInfo]);

            return [
                'auth_code' => $authCode,   //将登录码返回给前端
            ];
        }

        throw_if(! $request->has('auth_code'), WechatMiniProgramAuthException::class, 'no auth_code');

        $authCode = $request->input('auth_code');
        throw_if(!session()->has($authCode), WechatMiniProgramAuthException::class, 'auth_code not found');

        return $next($request);
    }

    protected function generateAuthCode($openid, $timestamp)
    {
        return md5($openid . $timestamp);
    }
}