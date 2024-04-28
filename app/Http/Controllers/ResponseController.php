<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public const ResponseMessage = [
        400 => '非法参数',
        401 => '未登陆',
        403 => '权限不足'
    ];

    public static function response($code, $message = "未提供消息", $data = null)
    {
        return response()->json(
            [
                'code'    => $code,
                'message' => self::ResponseMessage[$code] ?: $message,
                'data'    => $data
            ],
            $code
        );
    }
}
