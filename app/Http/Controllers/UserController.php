<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(403);
        }

        // 是否开启 验证码
        if (config("94list.captcha.use") !== '') {
            // 使用验证码
        }
    }

    public function register(Request $request)
    {
    }
}
