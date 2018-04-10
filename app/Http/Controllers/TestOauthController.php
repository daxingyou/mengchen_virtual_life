<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestOauthController extends Controller
{
    public function testOauth(Request $request)
    {
        return 'oauth success';
    }
}
