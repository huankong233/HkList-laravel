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
            "sleep"            => "required|numeric",
            "max_once"         => "required|numeric",
            "password"         => "required|string",
            "announce"         => "required|string",
            "user_agent"       => "required|string",
            "need_inv_code"    => "required|bool",
            "whitelist_mode"   => "required|bool",
            "debug"            => "required|bool",
            "name"             => "required|string",
            "main_server"      => "required|string",
            "code"             => "required|string",
            "show_copyright"   => "required|bool",
            "custom_copyright" => "required|string",
            "parse_mode"       => "required|numeric",
            "max_filesize"     => "required|numeric",
            "min_single_file"  => "required|numeric",
            "token_mode"       => "required|bool",
            "button_link"      => "required|string",
            "limit_cn"         => "required|bool",
            "limit_prov"       => "required|bool"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        $update["_94LIST_SLEEP"]               = $request["sleep"];
        $update["_94LIST_MAX_ONCE"]            = $request["max_once"];
        $update["_94LIST_PASSWORD"]            = '"' . $request["password"] . '"';
        $update["_94LIST_ANNOUNCE"]            = '"' . htmlspecialchars(str_replace("\n", "[NextLine]", $request["announce"]), ENT_QUOTES) . '"';
        $update["_94LIST_USER_AGENT"]          = '"' . $request["user_agent"] . '"';
        $update["_94LIST_NEED_INV_CODE"]       = $request["need_inv_code"];
        $update["_94LIST_WHITELIST_MODE"]      = $request["whitelist_mode"];
        $update["APP_DEBUG"]                   = $request["debug"];
        $update["APP_NAME"]                    = '"' . $request["name"] . '"';
        $update["_94LIST_MAIN_SERVER"]         = $request["main_server"];
        $update["_94LIST_CODE"]                = $request["code"];
        $update["_94LIST_SHOW_COPYRIGHT"]      = $request["show_copyright"];
        $update["_94LIST_PARSE_MODE"]          = $request["parse_mode"];
        $update["_94LIST_MAX_FILESIZE"]        = $request["max_filesize"];
        $update["_94LIST_CUSTOM_COPYRIGHT"]    = '"' . $request["custom_copyright"] . '"';
        $update["_94LIST_MIN_SINGLE_FILESIZE"] = $request["min_single_file"];
        $update["_94LIST_TOKEN_MODE"]          = $request["token_mode"];
        $update["_94LIST_BUTTON_LINK"]         = '"' . $request["button_link"] . '"';
        $update["_94LIST_LIMIT_CN"]            = $request["limit_cn"];
        $update["_94LIST_LIMIT_PROV"]          = $request["limit_prov"];

        if ($request["parse_mode"] === 4) $update["_94LIST_USER_AGENT"] = "netdisk;P2SP;3.0.10.22";

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
