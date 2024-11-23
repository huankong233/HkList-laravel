<?php

namespace App\Http\Controllers;

use App\Models\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function getAccounts(Request $request)
    {
        $accounts = Account::query()
            ->withCount([
                'records as total_count',
                'records as today_count' => function ($query) {
                    $query->whereDate('created_at', Carbon::today(config("app.timezone")));
                }
            ])
            ->withSum([
                'records as total_size' => function ($query) {
                    $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id');
                },
                'records as today_size' => function ($query) {
                    $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id')
                        ->whereDate('records.created_at', Carbon::today(config("app.timezone")));
                }
            ], "file_lists.size")
            ->paginate($request["size"]);

        return ResponseController::success($accounts);
    }

    public static function _getAccountInfo($type, $cookie)
    {
        $http = new Client([
            "headers" => [
                "User-Agent" => config("94list.fake_user_agent"),
                "cookie" => $type === 2 ? "" : $cookie
            ]
        ]);

        try {
            $res = $http->get("https://pan.baidu.com/rest/2.0/xpan/nas", ["query" => ["method" => "uinfo", "access_token" => $type === 2 ? $cookie : ""]]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取百度账户信息");
        }

        if (isset($response["errmsg"]) && $response["errmsg"] === "Invalid Bduss") return ResponseController::accountExpired();
        return $response ? ResponseController::success($response) : ResponseController::getAccountInfoFailed();
    }

    public static function _getAccessToken($refresh_token)
    {
        // 如果返回值是 -6 或 111 需要刷新 access_token
        $http = new Client([
            "headers" => [
                "User-Agent" => "pan.baidu.com"
            ]
        ]);

        try {
            $res = $http->get("https://openapi.baidu.com/oauth/2.0/token", ["query" => ["grant_type" => "refresh_token", "refresh_token" => $refresh_token, "client_id" => "iYCeC9g08h5vuP9UqvPHKKSVrKFXGa1v", "client_secret" => "jXiFMOPVPCWlO2M5CwWQzffpNPaGTRBG"]]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("换取AccessToken");
        }

        if (isset($response["error_description"])) return ResponseController::getAccessTokenFailed($response["error_description"]);
        return $response ? ResponseController::success($response) : ResponseController::getAccessTokenFailed();
    }

    public static function _getSvipEndAt($type, $cookie)
    {
        $http = new Client([
            "headers" => [
                "User-Agent" => config("94list.fake_user_agent"),
                "Cookie" => $type === 2 ? "" : $cookie
            ]
        ]);

        try {
            $res = $http->get("https://pan.baidu.com/rest/2.0/membership/user", ["query" => ["method" => "query", "clienttype" => 0, "app_id" => 250528, "web" => 1, "access_token" => $type === 2 ? $cookie : ""]]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取SVIP到期时间");
        }

        if (isset($response["errmsg"]) && $response["errmsg"] === "Invalid Bduss") return ResponseController::accountExpired();
        return $response ? ResponseController::success($response) : ResponseController::getSvipEndTimeFailed();
    }

    public static function _getAccountItems($type, $cookie)
    {
        if ($type === 3) {
            $http = new Client([
                "headers" => [
                    "User-Agent" => config("94list.fake_user_agent"),
                    "Cookie" => $cookie
                ]
            ]);

            try {
                $res = $http->get("https://pan.baidu.com/mid_enterprise_v2/api/enterprise/organization/allorganizationinfo", ["query" => ["clienttype" => 0, "app_id" => 24029990]]);
                $response = JSON::decode($res->getBody()->getContents());
            } catch (RequestException $e) {
                $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
            } catch (GuzzleException $e) {
                return ResponseController::networkError("获取SVIP到期时间");
            }

            if (isset($response["errmsg"]) && $response["errmsg"] === "Invalid Bduss") return ResponseController::accountExpired();
            if (!isset($response["data"][0]["cid"])) return ResponseController::accountIsNotEnterprise();
            $cid = $response["data"][0]["cid"];
        }

        $accountInfoRes = self::_getAccountInfo($type, $cookie);
        $accountInfoData = $accountInfoRes->getData(true);
        if ($accountInfoData["code"] !== 200) return $accountInfoRes;

        $vip_type = match ($accountInfoData["data"]["vip_type"]) {
            0 => "普通用户",
            1 => "普通会员",
            2 => "超级会员"
        };

        if ($vip_type === "超级会员") {
            $svipEndAtRes = self::_getSvipEndAt($type, $cookie);
            $svipEndAtData = $svipEndAtRes->getData(true);
            if ($svipEndAtData["code"] !== 200) return $svipEndAtRes;

            // 百度漏洞 svip到期后依然可用 #87
            if (isset($svipEndAtData["data"]["reminder"]["svip"])) {
                $timestamp = ($svipEndAtData["data"]["currenttime"] ?? 0) + ($svipEndAtData["data"]["reminder"]["svip"]["leftseconds"] ?? 0);
                $svip_end_at = Carbon::createFromTimestamp($timestamp, config("app.timezone"));
                if ($svip_end_at < now()) {
                    $switch = 0;
                    $reason = "账号会员已过期";
                }
            } else {
                $switch = 0;
                $reason = "获取会员到期时间失败";
            }
        }

        return ResponseController::success([
            "uk" => $accountInfoData["data"]["uk"],
            "baidu_name" => $accountInfoData["data"]["baidu_name"],
            "cid" => $cid ?? null,
            "cookie" => $cookie,
            "vip_type" => $vip_type,
            "switch" => $switch ?? 1,
            "reason" => $reason ?? "",
            "svip_end_at" => isset($svip_end_at) ? $svip_end_at->format('Y-m-d H:i:s') : null
        ]);
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "type" => ["required", Rule::in([1, 2, 3])],
            "cookie" => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $request["cookie"] = explode("\n", $request["cookie"]);

        $have_repeat = false;
        foreach ($request["cookie"] as $index => $cookie) {
            if (!$cookie) continue;

            if ($request["type"] === 2) {
                $cookie = self::_getAccessToken($cookie);
                $cookieData = $cookie->getData(true);
                if ($cookieData["code"] !== 200) return $cookie;
                $cookie = $cookieData["data"]["access_token"];
            }

            $accountItemsRes = self::_getAccountItems($request["type"], $cookie);
            $accountItemsData = $accountItemsRes->getData(true);
            if ($accountItemsData["code"] !== 200) return $accountItemsRes;

            $account = Account::query()->firstWhere("uk", $accountItemsData["data"]["uk"]);
            if (!$account) {
                if ($request["type"] === 2) {
                    $accountItemsData["data"]["account_type"] = "access_token";
                    $accountItemsData["data"]["access_token"] = $cookieData["data"]["access_token"];
                    $accountItemsData["data"]["refresh_token"] = $cookieData["data"]["refresh_token"];
                    $accountItemsData["data"]["expired_at"] = now()->addSeconds($cookieData["data"]["expires_in"]);
                    $accountItemsData["data"]["cookie"] = null;
                } else if ($request["type"] === 3) {
                    $accountItemsData["data"]["account_type"] = "enterprise";
                }
                Account::query()->create($accountItemsData["data"]);
            } else {
                $have_repeat = true;
            }

            if ($index < count($accountItemsData["data"]) - 1) sleep(1);
        }

        return ResponseController::success(["have_repeat" => $have_repeat]);
    }

    public static function updateAccount(Request $request, $account_id)
    {
        $validator = Validator::make($request->all(), [
            "baidu_name" => "required|string",
            "account_type" => ["required", Rule::in(["cookie", "access_token", "enterprise"])],
            "access_token" => "nullable|string",
            "refresh_token" => "nullable|string",
            "cookie" => "nullable|string",
            "vip_type" => ["required", Rule::in(["超级会员", "假超级会员", "普通会员", "普通用户"])],
            "switch" => "required|boolean",
            "prov" => ["nullable", Rule::in(["北京市", "天津市", "上海市", "重庆市", "河北省", "山西省", "内蒙古自治区", "辽宁省", "吉林省", "黑龙江省", "江苏省", "浙江省", "安徽省", "福建省", "江西省", "山东省", "河南省", "湖北省", "湖南省", "广东省", "广西壮族自治区", "海南省", "四川省", "贵州省", "云南省", "西藏自治区", "陕西省", "甘肃省", "青海省", "宁夏回族自治区", "新疆维吾尔自治区", "香港特别行政区", "澳门特别行政区", "台湾省"])],
            "reason" => "string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();

        $account->update([
            "baidu_name" => $request["baidu_name"],
            "account_type" => $request["account_type"],
            "access_token" => $request["access_token"],
            "refresh_token" => $request["refresh_token"],
            "cookie" => $request["cookie"],
            "vip_type" => $request["vip_type"],
            "switch" => $request["switch"],
            "prov" => $request["prov"],
            "reason" => $request["reason"]
        ]);

        return ResponseController::success();
    }

    public static function updateAccountInfo($account_id)
    {
        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();

        if ($account["account_type"] === "cookie") {
            $type = 1;
        } else if ($account["account_type"] === "access_token") {
            $type = 2;
        } else if ($account["account_type"] === "enterprise") {
            $type = 3;
        } else {
            $type = 1;
        }

        if ($type === 2) {
            $token = self::_getAccessToken($account["refresh_token"]);
            $tokenData = $token->getData(true);
            if ($tokenData["code"] !== 200) return $token;
            $cookie = $tokenData["data"]["access_token"];
        } else {
            // fallback type => 1
            $cookie = $account["cookie"];
        }

        $accountItemsRes = self::_getAccountItems($type, $cookie);
        $accountItemsData = $accountItemsRes->getData(true);

        if ($accountItemsData["code"] === 200) {
            if ($type === 2) {
                $accountItemsData["data"]["account_type"] = "access_token";
                $accountItemsData["data"]["access_token"] = $tokenData["data"]["access_token"];
                $accountItemsData["data"]["refresh_token"] = $tokenData["data"]["refresh_token"];
                $accountItemsData["data"]["expired_at"] = now()->addSeconds($tokenData["data"]["expires_in"]);
                $accountItemsData["data"]["cookie"] = null;
            }
            $account->update($accountItemsData["data"]);
        } else {
            $account->update([
                "switch" => 0,
                "reason" => "cookie可能已过期"
            ]);
        }

        return ResponseController::success();
    }

    public static function updateAccountsInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_ids" => "required|array",
            "account_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request["account_ids"] as $index => $account_id) {
            $res = self::updateAccountInfo($account_id);
            $resData = $res->getData(true);
            if ($resData["code"] !== 200) return $res;
            if ($index < count($request["account_ids"]) - 1) sleep(1);
        }

        return ResponseController::success();
    }

    public function switchAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_ids" => "required|array",
            "account_ids.*" => "required|numeric",
            "switch" => "required|boolean"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Account::query()
            ->whereIn("id", $request["account_ids"])
            ->update([
                "switch" => $request["switch"],
                "reason" => $request["switch"] ? "" : "用戶操作"
            ]);

        return ResponseController::success();
    }

    public function switchBanAccounts(Request $request)
    {
        Account::query()
            ->where([
                "switch" => 0,
                "reason" => "账号被限速"
            ])
            ->update([
                "switch" => 1,
                "reason" => ""
            ]);

        return ResponseController::success();
    }

    public function removeAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_ids" => "required|array",
            "account_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (in_array(0, $request["account_ids"])) return ResponseController::accountCanNotBeDeleted();

        Account::query()->whereIn("id", $request["account_ids"])->delete();

        return ResponseController::success();
    }

    public function getAccountsBan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_id" => "required|numeric",
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $account = Account::query()->find($request["account_id"]);
        if (!$account) return ResponseController::accountNotExists();

        $http = new Client([
            "headers" => [
                "User-Agent" => config("94list.fake_user_agent"),
                "cookie" => $account["account_type"] === "cookie" || $account["account_type"] === "enterprise" ? $account["cookie"] : ""
            ]
        ]);

        try {
            $res = $http->get("https://pan.baidu.com/api/checkapl/download", [
                "query" => [
                    "clienttype" => 8,
                    "channel" => "Windows%5f10%2e0%2e19045%5fUniyunguanjia%5fnetdisk%5f00000000000000000000000000000002",
                    "version" => "4.32.1",
                    "devuid" => "",
                    "access_token" => $account["account_type"] === "cookie" || $account["account_type"] === "enterprise" ? "" : $account["access_token"]
                ]
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取百度账户信息");
        }

        return ResponseController::success($response);
    }
}
