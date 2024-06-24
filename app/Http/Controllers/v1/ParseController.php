<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilsController;
use App\Models\Account;
use App\Models\FileList;
use App\Models\Group;
use App\Models\Record;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ParseController extends Controller
{
    public function getConfig(Request $request)
    {
        $config = config("94list");

        return ResponseController::success([
            "show_announce"  => $config["announce"] !== null && $config["announce"] !== "",
            "announce"       => $config["announce"],
            "user_agent"     => $config["user_agent"],
            "debug"          => config("app.debug"),
            "max_once"       => $config["max_once"],
            "have_account"   => self::getRandomCookie()->getData(true)["code"] === 200,
            "have_login"     => Auth::check(),
            "need_inv_code"  => $config["need_inv_code"],
            "need_password"  => $config["password"] !== "",
            "show_copyright" => $config["show_copyright"],
        ]);
    }

    public function getRandomCookie($vipType = ["超级会员"])
    {
        $vipType = is_array($vipType) ? $vipType : [$vipType];

        if (in_array("超级会员", $vipType)) {
            // 禁用不可用的账户
            $banAccounts = Account::query()
                                  ->where([
                                      "switch"   => 1,
                                      "vip_type" => "超级会员",
                                  ])
                                  ->whereDate("svip_end_at", "<", now())
                                  ->whereTime("svip_end_at", "<", now())
                                  ->get();

            $updateFailedAccounts = [];

            if ($banAccounts->count() !== 0) {
                // 更新账户状态
                foreach ($banAccounts as $account) {
                    $updateRes  = AccountController::updateAccount($account["id"]);
                    $updateData = $updateRes->getData(true);
                    if ($updateData["code"] !== 200) {
                        $account->update([
                            "switch" => 0,
                            "reason" => $updateData["message"]
                        ]);
                        $updateFailedAccounts[] = $account->toJson();
                    }
                    sleep(1);
                }

                if (config("mail.switch")) {
                    try {
                        Mail::raw("亲爱的" . config("mail.to.name") . ":\n\t有账户已过期,详见:" . JSON::encode($updateFailedAccounts), function ($message) {
                            $message->to(config("mail.to.address"))->subject("有账户过期了~");
                        });
                    } catch (Exception $e) {
                        return ResponseController::sendMailFailed($e->getMessage());
                    }
                }
            }
        }

        $account = Account::query()
                          ->where("switch", 1)
                          ->where(function (Builder $query) use ($vipType) {
                              foreach ($vipType as $type) {
                                  $query->orWhere("vip_type", $type);
                              }
                          })
                          ->inRandomOrder()
                          ->first();

        if (!$account) return ResponseController::svipAccountIsNotEnough(true, $vipType);

        return ResponseController::success($account);
    }

    public function decodeSceKey($seckey)
    {
        $seckey = str_replace("-", "+", $seckey);
        $seckey = str_replace("~", "=", $seckey);
        return str_replace("_", "/", $seckey);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "surl"  => "required|string",
            "dir"   => "required|string",
            "pwd"   => "string",
            "page"  => "numeric",
            "num"   => "numeric",
            "order" => Rule::in(["time", "filename"])
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http     = new Client([
                "headers" => [
                    "User-Agent" => config("94list.fake_wx_user_agent"),
                    "Cookie"     => config("94list.fake_cookie"),
                    "Referer"    => "https://pan.baidu.com/disk/home"
                ]
            ]);
            $res      = $http->post("https://pan.baidu.com/share/wxlist", [
                "query"       => [
                    "channel"    => "weixin",
                    "version"    => "2.9.6",
                    "clienttype" => 25,
                    "web"        => 1,
                    "qq-pf-to"   => "pcqq.c2c"
                ],
                "form_params" => [
                    "shorturl" => $request["surl"],
                    "dir"      => $request["dir"],
                    "root"     => $request["dir"] === "/" ? 1 : 0,
                    "pwd"      => $request["pwd"] ?? "",
                    "page"     => $request["page"] ?? 1,
                    "num"      => $request["num"] ?? 1000,
                    "order"    => $request["order"] ?? "time"
                ]
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取文件列表");
        }

        $errno = $response["errtype"] ?? ($response["errno"] ?? "未知");
        if (!isset($response["data"]["uk"]) | !isset($response["data"]["shareid"]) | !isset($response["data"]["seckey"]) | !isset($response["data"]["list"])) $errno = "缺少参数,请重试";

        if ($errno === 0) {
            // 保存所有文件到数据库
            foreach ($response["data"]["list"] as $file) {
                if ($file["isdir"] == 1 || $file["isdir"] == "1" || !isset($file["fs_id"]) || !isset($file["md5"])) continue;
                $find = FileList::query()->firstWhere("fs_id", $file["fs_id"]);
                if ($find) {
                    if ($find["md5"] !== $file["md5"]) {
                        $find->update([
                            "size" => $file["size"],
                            "md5"  => $file["md5"]
                        ]);
                    }
                } else {
                    FileList::query()->create([
                        "fs_id" => $file["fs_id"],
                        "size"  => $file["size"],
                        "md5"   => $file["md5"]
                    ]);
                }
            }
        }

        return match ($errno) {
            0                     => ResponseController::success([
                "uk"      => $response["data"]["uk"],
                "shareid" => $response["data"]["shareid"],
                "randsk"  => self::decodeSceKey($response["data"]["seckey"]),
                "list"    => $response["data"]["list"]
            ]),
            "mis_105"             => ResponseController::fileNotExists(),
            "mispw_9", "mispwd-9" => ResponseController::pwdWrong(),
            "mis_2", "mis_4"      => ResponseController::pathNotExists(),
            5                     => ResponseController::linkWrongOrPathNotExists(),
            3                     => ResponseController::linkNotValid(),
            10                    => ResponseController::linkIsOutDate(),
            8001, 9013, 9019      => ResponseController::cookieError($errno),
            default               => ResponseController::getFileListError($errno),
        };
    }

    public function checkLimit(Request $request)
    {
        // 获取今日解析数量
        $group = Group::query()
                      ->find(Auth::check() ? Auth::user()["group_id"] : -1);

        $records = Record::withTrashed()
                         ->where("ip", UtilsController::getIp())
                         ->whereDate("created_at", now())
                         ->get();

        if ($records->count() >= $group["count"] || $records->sum("size") >= $group["size"] * 1073741824) return ResponseController::groupQuotaHasBeenUsedUp();

        return ResponseController::success([
            "group_name" => $group["name"],
            "count"      => $group["count"] - $records->count(),
            "size"       => $group["size"] * 1073741824 - $records->sum("size")
        ]);
    }

    public function getDownloadLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "fs_ids"   => "required|array",
            "fs_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        // 不能超出最大解析限制
        if (count($request["fs_ids"]) > config("94list.max_once")) return ResponseController::linksOverloaded();

        // 检查限制还能不能解析
        $checkLimitRes  = self::checkLimit($request);
        $checkLimitData = $checkLimitRes->getData(true);
        if ($checkLimitData["code"] !== 200) return $checkLimitRes;

        // 检查文件数量是否符合用户组配额
        if (count($request["fs_ids"]) > $checkLimitData["data"]["count"]) return ResponseController::groupQuotaCountIsNotEnough();

        // 获取文件列表
        $fileList = FileList::query()->whereIn("fs_id", $request["fs_ids"])->get();

        if (count($fileList) !== count($request["fs_ids"])) return ResponseController::unknownFsId();

        // 检查文件大小是否符合用户组配额
        if (collect($fileList)->sum("size") > $checkLimitData["data"]["size"]) return ResponseController::groupQuotaSizeIsNotEnough();

        $parse_mode = config("94list.parse_mode");

        return match ($parse_mode) {
            1       => self::getDownloadLinksByDisk($request),
//            2       => self::getDownloadLinksByShare($request),
            2       => ResponseController::response(50000, 500, "暂未完成"),
            default => ResponseController::unknownParseMode()
        };
    }

    public function getDownloadLinksByDisk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "randsk"   => "required|string",
            "uk"       => "required|numeric",
            "shareid"  => "required|numeric",
            "fs_ids"   => "required|array",
            "fs_ids.*" => "required|numeric",
            "url"      => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $cookie     = self::getRandomCookie();
        $cookieData = $cookie->getData(true);
        if ($cookieData["code"] !== 200) return $cookie;

        $ua = config("94list.user_agent");

        try {
            $http = new Client();

            $res      = $http->post(config("94list.main_server") . "/api/parseUrl", [
                "json" => [
                    "type"     => 1,
                    "fsidlist" => $request["fs_ids"],
                    "code"     => config("94list.code"),
                    "cookie"   => $cookieData["data"]["cookie"],
                    "randsk"   => $request["randsk"],
                    "uk"       => $request["uk"],
                    "shareid"  => $request["shareid"],
                    "ua"       => $ua,
                    "url"      => $request["url"]
                ]
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = JSON::decode($e->getResponse()->getBody()->getContents());
            $reason   = $response["message"] ?? "未知原因,请重试";
            if ($reason !== "授权码已过期" && $reason !== "未知原因,请重试") {
                Account::query()
                       ->find($cookieData["data"]["id"])
                       ->update([
                           "switch" => 0,
                           "reason" => $reason,
                       ]);
            }
            return ResponseController::errorFromMainServer($reason);
        } catch (GuzzleException $e) {
            return ResponseController::networkError("连接解析服务器");
        }

        if (!$response) return ResponseController::errorFromMainServer("未知原因");
        if ($response["code"] !== 200) return ResponseController::errorFromMainServer($response["message"] ?? "未知原因");
        $responseData = $response["data"];

        Account::query()
               ->find($cookieData["data"]["id"])
               ->update([
                   "last_use_at" => date("Y-m-d H:i:s")
               ]);

        foreach ($responseData as $responseDatum) {
            RecordController::addRecord([
                "ip"                => UtilsController::getIp(),
                "fs_id"             => $responseDatum["fs_id"] ?? 0,
                "filename"          => $responseDatum["filename"],
                "user_id"           => Auth::user()["id"] ?? -1,
                "account_id"        => $cookieData["data"]["id"],
                "normal_account_id" => 0,
                "size"              => FileList::query()->firstWhere("fs_id", $responseDatum["fs_id"] ?? 0)["size"] ?? 0,
                "ua"                => $ua,
                "url"               => $responseDatum["url"]
            ]);
        }

        return ResponseController::success($responseData);
    }

    public function getDownloadLinksByShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "randsk"   => "required|string",
            "uk"       => "required|numeric",
            "shareid"  => "required|numeric",
            "fs_ids"   => "required|array",
            "fs_ids.*" => "required|numeric",
            "url"      => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $ua = config("94list.user_agent");

        $json = [
            "type"     => 2,
            "fsidlist" => $request["fs_ids"],
            "code"     => config("94list.code"),
            "randsk"   => $request["randsk"],
            "uk"       => $request["uk"],
            "shareid"  => $request["shareid"],
            "ua"       => $ua,
            "url"      => $request["url"]
        ];

        if ($request["vcode_input"] && $request["vcode_input"] !== "") {
            $validator = Validator::make($request->all(), [
                "vcode_input" => "required|string",
                "vcode_str"   => "required|string"
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $json["vcode_input"] = $request["vcode_input"];
            $json["vcode_str"]   = $request["vcode_str"];
        }

        $cookie     = self::getRandomCookie(["普通用户", "普通会员"]);
        $cookieData = $cookie->getData(true);
        if ($cookieData["code"] !== 200) return $cookie;
        $json["cookie1"] = $cookieData["data"]["cookie"];
        // 插入会员账号
        $json["cookie2"] = [];
        for ($i = 0; $i < count($request["fs_ids"]); $i++) {
            $cookie2     = self::getRandomCookie();
            $cookie2Data = $cookie2->getData(true);
            if ($cookie2Data["code"] !== 200) return $cookie2;
            $json["cookie2"][] = $cookie2Data["data"]["cookie"];
        }

        try {
            $http     = new Client();
            $res      = $http->post(config("94list.main_server") . "/api/parseUrl", ["json" => $json]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            dd($e->getResponse()->getBody()->getContents());
            $response = JSON::decode($e->getResponse()->getBody()->getContents());
            $reason   = $response["message"] ?? "未知原因,请重试";
            if ($reason !== "授权码已过期" && $reason !== "未知原因,请重试" && !str_contains("触发验证码", $reason)) {
                Account::query()
                       ->find($cookieData["data"]["id"])
                       ->update([
                           "switch" => 0,
                           "reason" => $reason,
                       ]);
            }
            return ResponseController::errorFromMainServer($reason);
        } catch (GuzzleException $e) {
            return ResponseController::networkError("连接解析服务器");
        }

        if (!$response) return ResponseController::errorFromMainServer("未知原因");
        if ($response["code"] !== 200) return ResponseController::errorFromMainServer($response["message"] ?? "未知原因");
        $responseData = $response["data"];

        Account::query()
               ->find($cookieData["data"]["id"])
               ->update([
                   "last_use_at" => date("Y-m-d H:i:s")
               ]);

        foreach ($responseData as $responseDatum) {
            RecordController::addRecord([
                "ip"                => UtilsController::getIp(),
                "fs_id"             => $responseDatum["fs_id"] ?? 0,
                "filename"          => $responseDatum["filename"],
                "user_id"           => Auth::user()["id"] ?? -1,
                "account_id"        => $cookieData["data"]["id"],
                "normal_account_id" => 0,
                "size"              => FileList::query()->firstWhere("fs_id", $responseDatum["fs_id"] ?? 0)["size"] ?? 0,
                "ua"                => $ua,
                "url"               => $responseDatum["url"]
            ]);
        }

        return ResponseController::success($responseData);
    }
}