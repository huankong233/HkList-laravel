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
                                   $query->whereDate('created_at', Carbon::today());
                               }
                           ])
                           ->withSum([
                               'records as total_size' => function ($query) {
                                   $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id');
                               },
                               'records as today_size' => function ($query) {
                                   $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id')
                                         ->whereDate('records.created_at', Carbon::today());
                               }
                           ], "file_lists.size")->paginate($request["size"]);

        return ResponseController::success($accounts);
    }

    public static function _getAccountInfo($cookie)
    {
        $http = new Client([
            "headers" => [
                "User-Agent" => config("94list.fake_user_agent"),
                "cookie"     => $cookie
            ]
        ]);

        try {
            $res      = $http->get("https://pan.baidu.com/rest/2.0/xpan/nas", ["query" => ["method" => "uinfo"]]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取百度账户信息");
        }

        if (isset($response["errmsg"]) && $response["errmsg"] === "Invalid Bduss") return ResponseController::accountExpired();
        return $response ? ResponseController::success($response) : ResponseController::getAccountInfoFailed();
    }

    public static function _getSvipEndAt($cookie)
    {
        $http = new Client([
            "headers" => [
                "User-Agent" => config("94list.fake_user_agent"),
                "Cookie"     => $cookie
            ]
        ]);

        try {
            $res      = $http->get("https://pan.baidu.com/rest/2.0/membership/user", ["query" => ["method" => "query", "clienttype" => 0, "app_id" => 250528, "web" => 1]]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取SVIP到期时间");
        }

        if (isset($response["errmsg"]) && $response["errmsg"] === "Invalid Bduss") return ResponseController::accountExpired();
        return $response ? ResponseController::success($response) : ResponseController::getSvipEndTimeFailed();
    }

    public static function _getAccountItems($cookie)
    {
        $accountInfoRes  = self::_getAccountInfo($cookie);
        $accountInfoData = $accountInfoRes->getData(true);
        if ($accountInfoData["code"] !== 200) return $accountInfoRes;

        $vip_type = match ($accountInfoData["data"]["vip_type"]) {
            0 => "普通用户",
            1 => "普通会员",
            2 => "超级会员"
        };

        if ($vip_type === "超级会员") {
            $svipEndAtRes  = self::_getSvipEndAt($cookie);
            $svipEndAtData = $svipEndAtRes->getData(true);
            if ($svipEndAtData["code"] !== 200) return $svipEndAtRes;

            // 百度漏洞 svip到期后依然可用 #87
            if (isset($svipEndAtData["data"]["reminder"]["svip"])) {
                $svip_end_at = Carbon::createFromTimestamp(($svipEndAtData["data"]["currenttime"] ?? 0) + ($svipEndAtData["data"]["reminder"]["svip"]["leftseconds"] ?? 0));
                if ($svip_end_at < now()) $switch = 0;
            } else {
                $vip_type = "假超级会员";
            }
        }

        return ResponseController::success([
            "baidu_name"  => $accountInfoData["data"]["baidu_name"],
            "cookie"      => $cookie,
            "vip_type"    => $vip_type,
            "switch"      => $switch ?? 1,
            "svip_end_at" => isset($svip_end_at) ? $svip_end_at->format('Y-m-d H:i:s') : null
        ]);
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cookie" => "required"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $request["cookie"] = is_array($request["cookie"]) ? $request["cookie"] : [$request["cookie"]];

        $have_repeat = false;
        foreach ($request["cookie"] as $cookie) {
            if (!$cookie) continue;

            $accountItemsRes  = self::_getAccountItems($cookie);
            $accountItemsData = $accountItemsRes->getData(true);
            if ($accountItemsData["code"] !== 200) return $accountItemsRes;

            $account = Account::query()->firstWhere("baidu_name", $accountItemsData["data"]["baidu_name"]);
            if (!$account) {
                Account::query()->create($accountItemsData["data"]);
            } else {
                $have_repeat = true;
            }

            sleep(1);
        }

        return ResponseController::success(["have_repeat" => $have_repeat]);
    }

    public static function updateAccount(Request $request, $account_id)
    {
        $validator = Validator::make($request->all(), [
            "prov" => ["nullable", Rule::in(["北京市", "天津市", "上海市", "重庆市", "河北省", "山西省", "内蒙古自治区", "辽宁省", "吉林省", "黑龙江省", "江苏省", "浙江省", "安徽省", "福建省", "江西省", "山东省", "河南省", "湖北省", "湖南省", "广东省", "广西壮族自治区", "海南省", "四川省", "贵州省", "云南省", "西藏自治区", "陕西省", "甘肃省", "青海省", "宁夏回族自治区", "新疆维吾尔自治区", "香港特别行政区", "澳门特别行政区", "台湾省"])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();

        $account->update(["prov" => $request["prov"]]);

        return ResponseController::success();
    }

    public static function updateAccountInfo($account_id)
    {
        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();

        $cookie           = $account["cookie"];
        $accountItemsRes  = self::_getAccountItems($cookie);
        $accountItemsData = $accountItemsRes->getData(true);

        if ($accountItemsData["code"] === 200) {
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
            "account_ids"   => "required|array",
            "account_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request["account_ids"] as $account_id) {
            self::updateAccountInfo($account_id);
            sleep(1);
        }

        return ResponseController::success();
    }

    public function switchAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_ids"   => "required|array",
            "account_ids.*" => "required|numeric",
            "switch"        => "required|boolean"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Account::query()
               ->whereIn("id", $request["account_ids"])
               ->update([
                   "switch" => $request["switch"],
                   "reason" => "用戶操作"
               ]);

        return ResponseController::success();
    }

    public function removeAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_ids"   => "required|array",
            "account_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Account::query()->whereIn("id", $request["account_ids"])->delete();

        return ResponseController::success();
    }
}
