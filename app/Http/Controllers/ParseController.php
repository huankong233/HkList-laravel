<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FileList;
use App\Models\Group;
use App\Models\InvCode;
use App\Models\Record;
use App\Models\Token;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ParseController extends Controller
{
    public function getConfig(Request $request)
    {
        $config = config("94list");

        $parse_mode = $config["parse_mode"];
        $account_type = ["超级会员"];
        if ($parse_mode === 11) {
            $account_type = ["超级会员", "普通会员", "普通用户"];
        } else if ($parse_mode === 12) {
            $account_type = ["普通用户"];
        }
        $cookie = self::getRandomCookie($account_type, false);
        $cookieData = $cookie->getData(true);

        return ResponseController::success([
            "show_announce" => $config["announce"] !== null && $config["announce"] !== "",
            "announce" => $config["announce"],
            "debug" => config("app.debug"),
            "max_once" => $config["max_once"],
            "have_account" => $cookieData["code"] === 200,
            "have_login" => Auth::check(),
            "need_inv_code" => $config["need_inv_code"],
            "need_password" => $config["password"] !== "",
            "show_copyright" => $config["show_copyright"],
            "custom_copyright" => $config["custom_copyright"],
            "min_single_filesize" => $config["min_single_filesize"],
            "max_single_filesize" => $config["max_single_filesize"],
            "token_mode" => $config["token_mode"],
            "button_link" => $config["button_link"],
            "show_login_button" => $config["show_login_button"]
        ]);
    }

    public function _getProvinceFromIP($ip): string|null|false
    {
        if ($ip === "0.0.0.0") return "上海市";

        $http = new Client([
            "headers" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0"
            ]
        ]);

        try {
            $res = $http->get("https://api.qjqq.cn/api/district", ["query" => ["ip" => $ip]]);
            $response = JSON::decode($res->getBody()->getContents());

            if (isset($response["code"]) && $response["code"] === 200 && isset($response["data"]["country"]) && isset($response["data"]["prov"])) {
                if (config("94list.limit_cn")) {
                    if ($response["data"]["country"] !== "中国") {
                        return false;
                    }
                }

                return $response["data"]["prov"] !== "" ? $response["data"]["prov"] : null;
            }
        } catch (Exception $e) {
        }

        try {
            $res = $http->get("https://www.ip.cn/api/index", ["query" => ["ip" => $ip, "type" => 1]]);
            $response = JSON::decode($res->getBody()->getContents());

            if (isset($response["rs"]) && $response["rs"] !== 1 && isset($response["address"])) {
                if (config("94list.limit_cn")) {
                    if (str_contains($response["address"], "中国")) {
                        return false;
                    }
                }

                return $response["address"] !== "" ? $response["address"] : null;
            }
        } catch (Exception $e) {
        }

        try {
            $res = $http->get("https://qifu.baidu.com/ip/geo/v1/district", ["query" => ["ip" => $ip]]);
            $response = JSON::decode($res->getBody()->getContents());

            if (isset($response["code"]) && $response["code"] === "Success" && isset($response["data"]["country"]) && isset($response["data"]["prov"])) {
                if (config("94list.limit_cn")) {
                    if ($response["data"]["country"] !== "中国") {
                        return false;
                    }
                }

                return $response["data"]["prov"] !== "" ? $response["data"]["prov"] : null;
            }
        } catch (Exception $e) {
        }

        return null;
    }

    // 省份标准名称映射表
    const provinces = [
        "北京" => "北京市",
        "天津" => "天津市",
        "上海" => "上海市",
        "重庆" => "重庆市",
        "河北" => "河北省",
        "山西" => "山西省",
        "内蒙古" => "内蒙古自治区",
        "辽宁" => "辽宁省",
        "吉林" => "吉林省",
        "黑龙江" => "黑龙江省",
        "江苏" => "江苏省",
        "浙江" => "浙江省",
        "安徽" => "安徽省",
        "福建" => "福建省",
        "江西" => "江西省",
        "山东" => "山东省",
        "河南" => "河南省",
        "湖北" => "湖北省",
        "湖南" => "湖南省",
        "广东" => "广东省",
        "广西" => "广西壮族自治区",
        "海南" => "海南省",
        "四川" => "四川省",
        "贵州" => "贵州省",
        "云南" => "云南省",
        "西藏" => "西藏自治区",
        "陕西" => "陕西省",
        "甘肃" => "甘肃省",
        "青海" => "青海省",
        "宁夏" => "宁夏回族自治区",
        "新疆" => "新疆维吾尔自治区",
        "香港" => "香港特别行政区",
        "澳门" => "澳门特别行政区",
        "台湾" => "台湾省"
    ];

    public function getProvinceFromIP($ip)
    {
        $prov = self::_getProvinceFromIP($ip);
        if ($prov === null || $prov === false) return $prov;

        // 去除多余的空白字符
        $name = trim($prov);

        // 匹配并返回标准省份名称
        foreach (self::provinces as $key => $standardName) {
            if (str_contains($name, $key)) return $standardName;
        }

        // 无匹配
        return null;
    }

    public static function _getRandomCookie($prov, $vipType, $account_type)
    {
        $where = [
            "switch" => 1,
            "account_type" => $account_type
        ];
        if (config("94list.limit_prov")) $where["prov"] = $prov;
        return Account::query()
            ->where($where)
            ->whereIn("vip_type", $vipType)
            ->leftJoin("records", function ($join) {
                $join->on("accounts.id", "=", "records.account_id")->whereDate("records.created_at", "=", now());
            })
            ->leftJoin("file_lists", function ($join) {
                $join->on("records.fs_id", "=", "file_lists.id");
            })
            ->select('accounts.*', DB::raw('IFNULL(SUM(file_lists.size), 0) as total_size'))
            ->groupBy(
                'accounts.id',
                'accounts.baidu_name',
                'accounts.account_type',
                'accounts.cookie',
                'accounts.uk',
                'accounts.access_token',
                'accounts.refresh_token',
                'accounts.cid',
                'accounts.expired_at',
                'accounts.vip_type',
                'accounts.switch',
                'accounts.reason',
                'accounts.prov',
                'accounts.svip_end_at',
                'accounts.last_use_at',
                'accounts.created_at',
                'accounts.updated_at',
                'accounts.deleted_at'
            )
            ->having('total_size', '<', config('94list.max_filesize'))
            ->inRandomOrder()
            ->first();
    }

    public function getRandomCookie($vipType = ["超级会员"], $makeNew = true)
    {
        $ip = UtilsController::getIp();
        if (config("94list.limit_cn") || config("94list.limit_prov")) {
            $prov = self::getProvinceFromIP($ip);
            if ($prov === false) return ResponseController::unsupportNotCNCountry();
        } else {
            $prov = null;
        }

        $account_type = in_array(config("94list.parse_mode"), [5, 10]) ? "access_token" : "cookie";
        if (config("94list.parse_mode") === 11) $account_type = "enterprise";

        if (in_array("超级会员", $vipType)) {
            // 刷新令牌
            $refreshAccounts = Account::query()
                ->where([
                    "switch" => 1,
                    "vip_type" => $vipType,
                    "account_type" => $account_type,
                    ["expired_at", "<", Carbon::now(config("app.timezone"))->addDays(10)],
                ])
                ->get();

            if ($refreshAccounts->count() !== 0) {
                foreach ($refreshAccounts as $index => $refreshAccount) {
                    $updateRes = AccountController::updateAccountInfo($refreshAccount["id"]);
                    $updateData = $updateRes->getData(true);
                    if ($updateData["code"] !== 200) {
                        $refreshAccount->update([
                            "switch" => 0,
                            "reason" => $updateData["message"]
                        ]);
                    }
                    if ($index < $refreshAccount->count() - 1) sleep(0.5);
                }
            }

            // 禁用过期的账户
            $banAccounts = Account::query()
                ->where([
                    "switch" => 1,
                    "vip_type" => "超级会员",
                    ["svip_end_at", "<", Carbon::now(config("app.timezone"))]
                ])
                ->get();

            if ($banAccounts->count() !== 0) {
                $updateFailedAccounts = [];

                // 更新账户状态
                foreach ($banAccounts as $index => $account) {
                    $updateRes = AccountController::updateAccountInfo($account["id"]);
                    $updateData = $updateRes->getData(true);
                    if ($updateData["code"] !== 200) {
                        $account->update([
                            "switch" => 0,
                            "reason" => $updateData["message"]
                        ]);
                        $updateFailedAccounts[] = $account->toJson();
                    }
                    if ($index < $banAccounts->count() - 1) sleep(0.5);
                }

                if (count($updateFailedAccounts) > 0) UtilsController::sendMail("有账户已过期,详见:" . JSON::encode($updateFailedAccounts));
            }
        }

        // 判斷是否獲取到了省份
        if ($prov !== null) {
            $account = self::_getRandomCookie($prov, $vipType, $account_type);

            if ($account === null) {
                $account = self::_getRandomCookie(null, $vipType, $account_type);

                if ($makeNew) {
                    $account?->update([
                        "prov" => $prov,
                    ]);
                }
            }
        } else {
            $account = self::_getRandomCookie(null, $vipType, $account_type);
        }

        if (!$account) return ResponseController::svipAccountIsNotEnough(true, $vipType);

        return ResponseController::success($account);
    }

    public function checkLimit(Request $request)
    {
        if (config("94list.token_mode") && isset($request["token"]) && $request["token"] !== "") {
            $validator = Validator::make($request->all(), [
                "token" => "required|string",
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $token = Token::query()->firstWhere("name", $request["token"]);
            if (!$token) return ResponseController::TokenNotExists();

            if ($token["ip"] !== null && $token["ip"] !== UtilsController::getIp()) return ResponseController::TokenIpIsNotMatch();

            // 检查是否已经过期
            if ($token["expired_at"] !== null && $token["expired_at"] < now()) return ResponseController::TokenExpired();

            $records = Record::query()
                ->where("token_id", $token["id"])
                ->leftJoin("file_lists", "file_lists.id", "=", "records.fs_id")
                ->selectRaw("SUM(size) as size,COUNT(*) as count")
                ->first();

            if ($records["count"] >= $token["count"] || $records["size"] >= $token["size"] * 1073741824) return ResponseController::TokenQuotaHasBeenUsedUp();

            return ResponseController::success([
                "group_name" => $token["name"],
                "count" => $token["count"] - $records["count"],
                "size" => $token["size"] * 1073741824 - $records["size"],
                "expired_at" => $token["expired_at"] ?? "未使用"
            ]);
        }

        // 获取今日解析数量
        $group = Group::withTrashed()->find(Auth::check() ? InvCode::withTrashed()->find(Auth::user()["inv_code_id"])["group_id"] : 1);

        $records = Record::query()
            ->where([
                "user_id" => Auth::check() ? Auth::user()["id"] : 1,
                "ip" => UtilsController::getIp()
            ])
            ->whereDate("records.created_at", now())
            ->leftJoin("file_lists", "file_lists.id", "=", "records.fs_id")
            ->selectRaw("SUM(size) as size,COUNT(*) as count")
            ->first();

        if ($records["count"] >= $group["count"] || $records["size"] >= $group["size"] * 1073741824) return ResponseController::groupQuotaHasBeenUsedUp();

        return ResponseController::success([
            "group_name" => $group["name"],
            "count" => $group["count"] - $records["count"],
            "size" => $group["size"] * 1073741824 - $records["size"]
        ]);
    }

    public function decodeSecKey($seckey)
    {
        $seckey = str_replace("-", "+", $seckey);
        $seckey = str_replace("~", "=", $seckey);
        return str_replace("_", "/", $seckey);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "surl" => "required|string",
            "dir" => "required|string",
            "pwd" => "string",
            "page" => "numeric",
            "num" => "numeric",
            "order" => Rule::in(["time", "filename"])
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http = new Client([
                "headers" => [
                    "User-Agent" => config("94list.fake_wx_user_agent"),
                    "Cookie" => config("94list.fake_cookie"),
                    "Referer" => "https://pan.baidu.com/disk/home"
                ]
            ]);
            $res = $http->post("https://pan.baidu.com/share/wxlist", [
                "query" => [
                    "channel" => "weixin",
                    "version" => "2.9.6",
                    "clienttype" => 25,
                    "web" => 1,
                    "qq-pf-to" => "pcqq.c2c"
                ],
                "form_params" => [
                    "shorturl" => $request["surl"],
                    "dir" => $request["dir"],
                    "root" => $request["dir"] === "/" ? 1 : 0,
                    "pwd" => $request["pwd"] ?? "",
                    "page" => $request["page"] ?? 1,
                    "num" => $request["num"] ?? 1000,
                    "order" => $request["order"] ?? "time"
                ],
                "timeout" => 99999
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取文件列表");
        }

        $errno = $response["errtype"] ?? ($response["errno"] ?? "未知");

        if ($errno === 0) {
            if (!isset($response["data"]["uk"]) | !isset($response["data"]["shareid"]) | !isset($response["data"]["seckey"]) | !isset($response["data"]["list"])) {
                $errno = "请检查链接是否有效并重试";
            } else {
                // 保存所有文件到数据库
                foreach ($response["data"]["list"] as $file) {
                    if ($file["isdir"] === 1 || $file["isdir"] === "1" || !isset($file["fs_id"]) || !isset($file["md5"])) continue;

                    $find = FileList::query()->firstWhere([
                        "surl" => $request["surl"],
                        "pwd" => $request["pwd"],
                        "fs_id" => $file["fs_id"]
                    ]);

                    if ($find) {
                        $find->update([
                            "size" => $file["size"],
                            "md5" => $file["md5"]
                        ]);
                    } else {
                        FileList::query()->create([
                            "surl" => $request["surl"],
                            "pwd" => $request["pwd"],
                            "fs_id" => $file["fs_id"],
                            "size" => $file["size"],
                            "filename" => $file["server_filename"],
                            "md5" => $file["md5"]
                        ]);
                    }
                }
            }
        }

        return match ($errno) {
            0 => ResponseController::success([
                "uk" => $response["data"]["uk"],
                "shareid" => $response["data"]["shareid"],
                "randsk" => self::decodeSecKey($response["data"]["seckey"]),
                "list" => $response["data"]["list"]
            ]),
            "mis_105" => ResponseController::fileNotExists(),
            "mispw_9", "mispwd-9" => ResponseController::pwdWrong(),
            "mis_2", "mis_4" => ResponseController::pathNotExists(),
            5 => ResponseController::linkWrongOrPathNotExists(),
            3 => ResponseController::linkNotValid(),
            10 => ResponseController::linkIsOutDate(),
            8001, 9013, 9019 => ResponseController::cookieError($errno),
            default => ResponseController::getFileListError($errno),
        };
    }

    public function getVcode()
    {
        try {
            $http = new Client([
                "headers" => [
                    "User-Agent" => config("94list.fake_user_agent"),
                    "Cookie" => config("94list.fake_cookie"),
                    "Host" => "pan.baidu.com",
                    "Origin" => "https://pan.baidu.com",
                    "Referer" => "https://pan.baidu.com/disk/home"
                ]
            ]);

            $response = $http->get("https://pan.baidu.com/api/getvcode", [
                "query" => [
                    "prod" => "pan",
                    "channel" => "chunlei",
                    "web" => 1,
                    "app_id" => 250528,
                    "clienttype" => 0
                ],
                "timeout" => 99999
            ]);

            $response = JSON::decode($response->getBody());

            return ResponseController::success([
                "img" => $response["img"],
                "vcode" => $response["vcode"]
            ]);
        } catch (RequestException $e) {
            return ResponseController::getVcodeError();
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取vcode");
        }
    }

    public static function xorEncrypt($data, $key)
    {
        $keyLength = strlen($key);
        $dataLength = strlen($data);
        $output = '';

        for ($i = 0; $i < $dataLength; $i++) {
            $output .= $data[$i] ^ $key[$i % $keyLength];
        }

        return bin2hex($output);
    }

    public function getDownloadLinks(Request $request)
    {
        set_time_limit(0);

        $validator = Validator::make($request->all(), [
            "randsk" => "required|string",
            "uk" => "required|numeric",
            "shareid" => "required|numeric",
            "fs_ids" => "required|array",
            "fs_ids.*" => "required|numeric",
            "url" => "required|string",
            "surl" => "required|string",
            "dir" => "required|string",
            "pwd" => "string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        // 不能超出最大解析限制
        if (count($request["fs_ids"]) > config("94list.max_once")) return ResponseController::linksOverloaded();

        // 检查限制还能不能解析
        $checkLimitRes = self::checkLimit($request);
        $checkLimitData = $checkLimitRes->getData(true);
        if ($checkLimitData["code"] !== 200) return $checkLimitRes;

        // 检查文件数量是否符合用户组配额
        if (count($request["fs_ids"]) > $checkLimitData["data"]["count"]) return ResponseController::groupQuotaCountIsNotEnough();

        // 检查链接是否有效
        $valid = self::getFileList($request);
        $validData = $valid->getData(true);
        if ($validData["code"] !== 200) return $valid;

        // 获取文件列表
        $fileList = FileList::query()
            ->where([
                "surl" => $request["surl"],
                "pwd" => $request["pwd"]
            ])
            ->whereIn("fs_id", $request["fs_ids"])
            ->get();

        if (count($fileList) !== count($request["fs_ids"])) return ResponseController::unknownFsId();

        foreach ($fileList as $file) {
            if ($file["size"] < config("94list.min_single_filesize")) {
                $request["fs_ids"] = array_filter($request["fs_ids"], fn($v) => $v === $file["fs_ids"]);
            }

            if ($file["size"] > config("94list.max_single_filesize")) {
                $request["fs_ids"] = array_filter($request["fs_ids"], fn($v) => $v === $file["fs_ids"]);
            }
        }

        if (count($request["fs_ids"]) === 0) return ResponseController::nullFile();

        // 检查文件大小是否符合用户组配额
        if (collect($fileList)->sum("size") > $checkLimitData["data"]["size"]) return ResponseController::groupQuotaSizeIsNotEnough();

        $parse_mode = config("94list.parse_mode");

        $ua = config("94list.user_agent");

        $json = [
            "type" => $parse_mode,
            "fsidlist" => $request["fs_ids"],
            "code" => config("94list.code"),
            "randsk" => $request["randsk"],
            "uk" => $request["uk"],
            "shareid" => $request["shareid"],
            "ua" => $ua,
            "url" => $request["url"],
            "surl" => $request["surl"],
            "dir" => $request["dir"],
            "pwd" => $request["pwd"],
        ];

        if (isset($request["vcode_input"]) && $request["vcode_input"] !== "") {
            $validator = Validator::make($request->all(), [
                "vcode_input" => "required|string",
                "vcode_str" => "required|string"
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $json["vcode_input"] = $request["vcode_input"];
            $json["vcode_str"] = $request["vcode_str"];
        }

        // 插入会员账号
        $json["cookie"] = [];

        // 检查是否指定了账号管理员
        if (isset($request["account_ids"]) && $request["account_ids"] !== "") {
            $user_id = Auth::check() ? Auth::user()["id"] : 1;
            $user = User::query()->find($user_id);
            $role = $user["role"];
            if ($role !== "admin") return ResponseController::permissionsDenied();

            // 判断是否只是一个文件
            if (count($request["fs_ids"]) > 1) return ResponseController::onlyOneFile();

            $json["fsidlist"] = [];
            $request["account_ids"] = explode(",", $request["account_ids"]);
            foreach ($request["account_ids"] as $account_id) {
                $account = Account::query()->find($account_id);
                if (!$account) return ResponseController::accountNotExists();
                $arr = ["id" => $account_id];
                if (in_array($parse_mode, [5, 10])) {
                    if ($account["account_type"] !== "access_token") return ResponseController::accountTypeWrong($account_id);
                    $arr["access_token"] = $account["access_token"];
                } else if ($parse_mode === 11) {
                    if ($account["account_type"] !== "enterprise") return ResponseController::accountTypeWrong($account_id);
                    $arr["cookie"] = $account["cookie"];
                    $arr["cid"] = $account["cid"];
                } else {
                    if ($account["account_type"] !== "cookie") return ResponseController::accountTypeWrong($account_id);
                    $arr["cookie"] = $account["cookie"];
                }
                $json["cookie"][] = $arr;
                $json["fsidlist"][] = $request["fs_ids"][0];
            }
        } else {
            for ($i = 0; $i < count($request["fs_ids"]); $i++) {
                $account_type = ["超级会员"];
                if ($parse_mode === 11) {
                    $account_type = ["超级会员", "普通会员", "普通用户"];
                } else if ($parse_mode === 12) {
                    $account_type = ["普通用户"];
                }
                $cookie = self::getRandomCookie($account_type);
                $cookieData = $cookie->getData(true);
                if ($cookieData["code"] !== 200) return $cookie;
                $arr = ["id" => $cookieData["data"]["id"]];
                if ($cookieData["data"]["account_type"] === "access_token") {
                    $arr["access_token"] = $cookieData["data"]["access_token"];
                } else if ($cookieData["data"]["account_type"] === "enterprise") {
                    $arr["cookie"] = $cookieData["data"]["cookie"];
                    $arr["cid"] = $cookieData["data"]["cid"];
                } else {
                    $arr["cookie"] = $cookieData["data"]["cookie"];
                }
                $json["cookie"][] = $arr;
            }
        }

        $start = microtime(true);

        try {
            $http = new Client();
            $res = $http->post(config("94list.main_server") . "/api/parseUrl", ["json" => $json]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = JSON::decode($e->getResponse()->getBody()->getContents());
            $reason = $response["message"] ?? "未知原因,请重试";
            if (str_contains($reason, "风控")) {
                $account = Account::query()->find($json["cookie"][0]["id"]);
                if ($account) {
                    $account->update([
                        "switch" => 0,
                        "reason" => $reason
                    ]);
                    UtilsController::sendMail("有账户被风控,账号ID:" . $json["cookie"][0]["id"]);
                }
            }
            return ResponseController::errorFromMainServer($reason);
        } catch (GuzzleException $e) {
            return ResponseController::networkError("连接解析服务器");
        }

        if (!$response) return ResponseController::errorFromMainServer("未知原因");
        if ($response["code"] !== 200) return ResponseController::errorFromMainServer($response["message"] ?? "未知原因");
        $responseData = $response["data"];

        if (isset($request["token"]) && $request["token"] !== "") {
            $token = Token::query()->firstWhere("name", $request["token"]);
            $token_id = $token["id"];
            $user_id = null;
        } else {
            $token = null;
            $token_id = null;
            $user_id = Auth::user()["id"] ?? 1;
        }

        $banned = [];
        $limited = [];
        $key = config("94list.proxy_password");
        if ($key === "") $key = "download";
        $server = config("94list.proxy_server");

        $data = array_map(function ($responseDatum) use ($ua, $parse_mode, $request, $user_id, $token, $token_id, &$banned, &$limited, $server, $key, $json) {
            $url = $responseDatum["url"];
            $res = [
                "url" => $url,
                "filename" => $responseDatum["filename"],
                "fs_id" => $responseDatum["fs_id"],
                "ua" => $responseDatum["ua"] ?? $ua
            ];

            if ($server !== "" && str_contains($url, "http")) {
                $json = JSON::encode(["url" => $url, "ua" => $ua]);
                $res["url"] = $server . "?data=" . self::xorEncrypt($json, $key);
            }

            $ck_id = $responseDatum["cookie_id"] ?? 0;
            if ($parse_mode === 11) $ck_id = $json["cookie"][0]["id"];
            $account = Account::query()->find($ck_id);

            if (isset($responseDatum["msg"]) && $responseDatum["msg"] === "获取成功") {
                if (str_contains($url, "qdall01")) {
                    $res["url"] = "账号被限速";

                    $account->update([
                        "last_use_at" => date("Y-m-d H:i:s"),
                        "switch" => 0,
                        "reason" => "账号被限速",
                    ]);

                    $limited[] = $ck_id;
                } else if (str_contains($url, "风控")) {
                    $res["url"] = "账号已风控";

                    $account->update([
                        "last_use_at" => date("Y-m-d H:i:s"),
                        "switch" => 0,
                        "reason" => $url,
                    ]);

                    $banned[] = $ck_id;
                } else {
                    $account->update(["last_use_at" => date("Y-m-d H:i:s")]);

                    if ($token) {
                        $token->update([
                            "expired_at" => $token["expired_at"] === null ? now()->addDays($token["day"]) : $token["expired_at"],
                            "ip" => config("94list.token_bind_ip") ? UtilsController::getIp() : null
                        ]);
                    }

                    $file = FileList::query()
                        ->firstWhere([
                            "surl" => $request["surl"],
                            "pwd" => $request["pwd"],
                            "fs_id" => $responseDatum["fs_id"]
                        ]);

                    $fs_id = $file["id"];

                    RecordController::addRecord([
                        "ip" => UtilsController::getIp(),
                        "fs_id" => $fs_id,
                        "url" => $url,
                        "ua" => $ua,
                        "user_id" => $user_id,
                        "token_id" => $token_id,
                        "account_id" => $ck_id
                    ]);

                    if (str_contains($url, "http") && isset($responseDatum["urls"])) {
                        $res["urls"] = array_map(function ($item) use ($server, $key, $ua) {
                            if ($server !== "") {
                                $json = JSON::encode(["url" => $item, "ua" => $ua]);
                                $item = $server . "?data=" . self::xorEncrypt($json, $key);
                            }
                            return $item;
                        }, $responseDatum["urls"]);
                    }
                }
            } else if (str_contains($url, "风控") || str_contains($url, "invalid")) {
                $account->update([
                    "switch" => 0,
                    "reason" => $url,
                ]);

                $banned[] = $ck_id;
            }

            return $res;
        }, $responseData);

        if (count($limited) !== 0) UtilsController::sendMail("有账户被限速,账号ID:" . JSON::encode($limited));
        if (count($banned) !== 0) UtilsController::sendMail("有账户被风控,账号ID:" . JSON::encode($banned));

        return ResponseController::success($data);
    }
}