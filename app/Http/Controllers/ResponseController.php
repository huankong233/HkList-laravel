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
        return self::response(10008, 404, '用户组不存在');
    }

    public static function groupExists()
    {
        return self::response(10009, 409, '用户组名称已存在');
    }

    public static function recordNotExists()
    {
        return self::response(10010, 404, '记录不存在');
    }

    public static function accountNotExists()
    {
        return self::response(10011, 404, '账户不存在');
    }

    public static function networkError($query)
    {
        return self::response(10013, 500, '在进行' . $query . '时出现网络错误,检查服务器网络状态');
    }

    public static function getAccountInfoFailed()
    {
        return self::response(10015, 404, '获取账户信息失败');
    }

    public static function getSvipEndTimeFailed()
    {
        return self::response(10015, 404, '获取SVIP到期时间失败');
    }

    public static function sendMailFailed($reason)
    {
        return self::response(10016, 500, '发送邮件失败' . ($reason ? ',原因:' . $reason : ''));
    }

    public static function parsePasswordError()
    {
        return self::response(10017, 403, '解析密码错误');
    }

    public static function fileNotExists()
    {
        return self::response(10018, 404, '文件不存在');
    }

    public static function pwdWrong()
    {
        return self::response(10019, 403, '提取码错误');
    }

    public static function pathNotExists()
    {
        return self::response(10020, 404, '不存在此目录');
    }

    public static function linkWrongOrPathNotExists()
    {
        return self::response(10021, 400, '不存在此分享链接或提取码错误');
    }

    public static function linkNotValid()
    {
        return self::response(10022, 403, '此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！');
    }

    public static function linkIsOutDate()
    {
        return self::response(10023, 403, '啊哦，来晚了，该分享文件已过期');
    }

    public static function cookieError($errno)
    {
        return self::response(10024, 500, 'fakeCookie失效,code:' . $errno);
    }

    public static function getFileListError($errno)
    {
        return self::response(10025, 500, '获取文件列表遇到未知错误,code:' . $errno);
    }

    public static function getSignError($errno)
    {
        return self::response(10026, 500, '获取文件签名遇到未知错误,code:' . $errno);
    }

    public static function linksOverloaded()
    {
        return self::response(10027, 403, '超出单次解析最大数量');
    }

    public static function normalAccountIsNotEnough()
    {
        return self::response(10028, 403, '普通用户账户不足');
    }

    public static function svipAccountIsNotEnough()
    {
        return self::response(10029, 403, '超级会员账户不足');
    }

    public static function accountHasBeenLimitOfTheSpeed()
    {
        return self::response(10030, 500, '账户被限速,请重新解析尝试!');
    }

    public static function getDlinkError($code)
    {
        return self::response(10031, 500, '在获取dlink时请求失败,code:' . $code);
    }

    public static function getRealLinkError()
    {
        return self::response(10032, 500, '在获取reallink时请求失败!');
    }

    public static function hitCaptcha($data)
    {
        return self::response(10033, 500, '触发验证码', $data);
    }

    public static function downloadError()
    {
        return self::response(10034, 500, '下载失败');
    }

    public static function ipHasBeenBaned()
    {
        return self::response(10035, 500, '服务器ip被拉黑');
    }

    public static function signIsOutDate()
    {
        return self::response(10036, 500, '签名过期');
    }

    public static function processFilesTooMuch()
    {
        return self::response(10037, 500, '操作的文件过多');
    }

    public static function invCodeNotExists()
    {
        return self::response(10038, 404, '邀请码不存在');
    }

    public static function invCodeExists()
    {
        return self::response(10039, 409, '邀请码名称已存在');
    }

    public static function notInWhiteList()
    {
        return self::response(10040, 403, '您不在白名单中!');
    }

    public static function inBlackList()
    {
        return self::response(10041, 403, '您在黑名单中!');
    }

    public static function IpNotExists()
    {
        return self::response(10042, 404, 'ip不存在');
    }

    public static function IpExists()
    {
        return self::response(10043, 409, 'ip名称已存在');
    }

    public static function groupQuotaHasBeenUsedUp()
    {
        return self::response(10044, 403, '用户组配额已用完');
    }

    public static function accountExpired()
    {
        return self::response(10045, 403, '账户cookie已失效');
    }

    public static function groupCanNotBeRemoved($reason)
    {
        return self::response(10046, 403, '用户组不能被删除,原因:' . $reason);
    }

    public static function getVCodeError($code)
    {
        return self::response(10047, 500, '在获取vcode时请求失败,code:' . $code);
    }

    public static function vcodeNotExists()
    {
        return self::response(10048, 404, 'vcode不存在');
    }
}
