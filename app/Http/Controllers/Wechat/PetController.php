<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat;

class PetController extends Controller
{
    protected $wechatApp;

    public function __construct(Request $request)
    {
        $this->wechatApp = EasyWeChat::miniProgram();

        parent::__construct($request);
    }

    public function interact(Request $request)
    {
        $user = session($request->input('auth_code'));
        return $user;
    }
}
