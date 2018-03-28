<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\WechatMiniProgramService;
use Illuminate\Http\Request;

class MiniProgramController extends Controller
{
    public function player(Request $request)
    {
        return WechatMiniProgramService::getPlayer($request);
    }

    public function res($msg)
    {
        return [
            'code' => -1,
            'message' => $msg,
        ];
    }
}
