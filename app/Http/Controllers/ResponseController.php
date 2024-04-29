<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public static function response($code, $statusCode, $message, $data = null)
    {
        return response()->json(
            [
                'code'    => $code,
                'message' => $message,
                'data'    => $data
            ],
            $statusCode
        );
    }

    public static function success($data = null)
    {
        return self::response(200, 200, "请求成功", $data);
    }

    public static function paramsError()
    {
        return self::response(10001, 400, '非法参数');
    }

    public static function accountExists()
    {
        return self::response(10002, 409, '账户已存在');
    }

    public static function accountNotExists()
    {
        return self::response(10003, 404, '账户不存在');
    }

    public static function accountPasswordError()
    {
        return self::response(10004, 403, '账户或密码错误');
    }

    public static function userNotLogin()
    {
        return self::response(10005, 403, '用户未登陆');
    }

    public static function permissionsDenied()
    {
        return self::response(10006, 403, '用户权限不足');
    }

    public static function captchaError()
    {
        return self::response(10007, 403, '验证码校验失败');
    }
}
