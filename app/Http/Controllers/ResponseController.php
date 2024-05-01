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
        return self::response(200, 200, '请求成功', $data);
    }

    public static function dbFileExists()
    {
        return self::response(10001, 500, '数据库文件已存在');
    }

    public static function paramsError()
    {
        return self::response(10002, 400, '非法参数');
    }

    public static function userExists()
    {
        return self::response(10003, 409, '用户名称已存在');
    }

    public static function userNotExists()
    {
        return self::response(10004, 404, '用户不存在');
    }

    public static function userPasswordError()
    {
        return self::response(10005, 403, '用户名或密码错误');
    }

    public static function userNotLogin()
    {
        return self::response(10006, 403, '用户未登陆');
    }

    public static function permissionsDenied()
    {
        return self::response(10007, 403, '用户权限不足');
    }

    public static function groupNotExists()
    {
        return self::response(10008, 404, '分组不存在');
    }

    public static function groupExists()
    {
        return self::response(10009, 409, '分组名称已存在');
    }

    public static function recordNotExists()
    {
        return self::response(10010, 404, '记录不存在');
    }

    public static function accountNotExists()
    {
        return self::response(10011, 404, '账户不存在');
    }

    public static function unknownCaptcha()
    {
        return self::response(10012, 500, '未知验证码驱动器');
    }

    public static function networkError($query = "未知")
    {
        return self::response(10013, 500, "在进行{$query}时出现网络错误,检查服务器网络状态");
    }

    public static function captchaSuccess()
    {
        return self::success();
    }

    public static function captchaFailed()
    {
        return self::response(10014, 400, '验证码校验失败');
    }

    public static function getAccountInfoFailed()
    {
        return self::response(10015, 404, '获取账户信息失败');
    }

    public static function getSvipEndTimeFailed()
    {
        return self::response(10015, 404, '获取SVIP到期时间失败');
    }

    public static function sendMailFailed($message = '')
    {
        return self::response(10016, 500, '发送邮件失败,原因:' . $message);
    }
}
