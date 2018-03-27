<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 获取登录用户信息
     *
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        return User::with(['group', 'parent',])->find(Auth::id());
    }

    public function getContentHeaderH1(Request $request)
    {
        return '';  //面包屑导航左边的标题文字
    }
}