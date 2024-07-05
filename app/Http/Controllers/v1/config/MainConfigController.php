<?php

namespace App\Http\Controllers\v1\config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\ResponseController;
use App\Models\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MainConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        return ResponseController::success([
            ...config("94list"),
            "debug" => config("app.debug"),
            "name"  => config("app.name")
        ]);
    }

    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "sleep"            => "numeric",
            "max_once"         => "numeric",
            "password"         => "string",
            "announce"         => "string",
            "user_agent"       => "string",
            "need_inv_code"    => "bool",
            "whitelist_mode"   => "bool",
            "debug"            => "bool",
            "name"             => "string",
            "main_server"      => "string",
            "code"             => "string",
            "show_copyright"   => "bool",
            "custom_copyright" => "string",
            "parse_mode"       => "numeric",
            "max_filesize"     => "numeric",
            "min_single_file"  => "numeric",
            "token_mode"       => "bool",
            "button_link"      => "string",
            "limit_cn"         => "bool",
            "limit_prov"       => "bool"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if (isset($request["sleep"])) $update["_94LIST_SLEEP"] = $request["sleep"];
        if (isset($request["max_once"])) $update["_94LIST_MAX_ONCE"] = $request["max_once"];
        if (isset($request["password"])) $update["_94LIST_PASSWORD"] = $request["password"] === "" ? "" : '"' . $request["password"] . '"';
        if (isset($request["announce"])) $update["_94LIST_ANNOUNCE"] = $request["announce"] === "" ? "" : '"' . htmlspecialchars(str_replace("\n", "[NextLine]", $request["announce"]), ENT_QUOTES) . '"';
        if (isset($request["user_agent"])) $update["_94LIST_USER_AGENT"] = '"' . $request["user_agent"] . '"';
        if (isset($request["need_inv_code"])) $update["_94LIST_NEED_INV_CODE"] = $request["need_inv_code"];
        if (isset($request["whitelist_mode"])) $update["_94LIST_WHITELIST_MODE"] = $request["whitelist_mode"];
        if (isset($request["debug"])) $update["APP_DEBUG"] = $request["debug"];
        if (isset($request["name"])) $update["APP_NAME"] = $request["name"];
        if (isset($request["main_server"])) $update["_94LIST_MAIN_SERVER"] = $request["main_server"];
        if (isset($request["code"])) $update["_94LIST_CODE"] = $request["code"];
        if (isset($request["show_copyright"])) $update["_94LIST_SHOW_COPYRIGHT"] = $request["show_copyright"];
        if (isset($request["parse_mode"])) $update["_94LIST_PARSE_MODE"] = $request["parse_mode"];
        if (isset($request["max_filesize"])) $update["_94LIST_MAX_FILESIZE"] = $request["max_filesize"];
        if (isset($request["custom_copyright"])) $update["_94LIST_CUSTOM_COPYRIGHT"] = $request["custom_copyright"] === "" ? "" : '"' . $request["custom_copyright"] . '"';
        if (isset($request["min_single_file"])) $update["_94LIST_MIN_SINGLE_FILESIZE"] = $request["min_single_file"];
        if (isset($request["token_mode"])) $update["_94LIST_TOKEN_MODE"] = $request["token_mode"];
        if (isset($request["button_link"])) $update["_94LIST_BUTTON_LINK"] = $request["button_link"] === "" ? "" : '"' . $request["button_link"] . '"';
        if (isset($request["limit_cn"])) $update["_94LIST_LIMIT_CN"] = $request["limit_cn"];
        if (isset($request["limit_prov"])) $update["_94LIST_LIMIT_PROV"] = $request["limit_prov"];

        if ($request["parse_mode"] === 4) $update["_94LIST_USER_AGENT"] = "netdisk;P2SP;2.2.101.161;netdisk;12.11.9;V2156A;android-android;11;JSbridge4.4.0;jointBridge;1.1.0;";

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }

    public function testAuth(Request $request)
    {
        $updateConfig     = self::updateConfig($request);
        $updateConfigData = $updateConfig->getData(true);
        if ($updateConfigData["code"] !== 200) return $updateConfig;

        $http = new Client();

        // 测试
        try {
            $res = $http->post(config("94list.main_server") . "/api/checkCode", ["query" => ["code" => config("94list.code")]]);
            return JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            try {
                $res      = $http->get(config("94list.main_server") . "/api/ip");
                $response = $res->getBody()->getContents();
            } catch (GuzzleException $e) {
                return ResponseController::networkError("连接解析服务器");
            }
            $errmsg               = JSON::decode($e->getResponse()->getBody()->getContents());
            $errmsg["data"]["ip"] = $response;
            return $errmsg;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("连接解析服务器");
        }
    }
}
