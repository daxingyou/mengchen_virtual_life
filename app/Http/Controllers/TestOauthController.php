<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestOauthController extends Controller
{
    public function testOauth(Request $request)
    {
        return 'oauth success';
    }
}
