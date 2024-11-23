<?php

namespace App\Http\Controllers\config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
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
            "max_once"            => "required|numeric",
            "password"            => "string",
            "announce"            => "string",
            "user_agent"          => "required|string",
            "need_inv_code"       => "required|bool",
            "whitelist_mode"      => "required|bool",
            "debug"               => "required|bool",
            "name"                => "required|string",
            "main_server"         => "string",
            "code"                => "string",
            "show_copyright"      => "required|bool",
            "custom_copyright"    => "string",
            "parse_mode"          => "required|numeric",
            "max_filesize"        => "required|numeric",
            "min_single_filesize" => "required|numeric",
            "max_single_filesize" => "required|numeric",
            "token_mode"          => "required|bool",
            "button_link"         => "string",
            "limit_cn"            => "required|bool",
            "limit_prov"          => "required|bool",
            "show_login_button"   => "required|bool",
            "token_bind_ip"       => "required|bool",
            "proxy_server"        => "string",
            "proxy_password"      => "string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        $update["_94LIST_MAX_ONCE"]            = $request["max_once"];
        $update["_94LIST_PASSWORD"]            = '"' . $request["password"] . '"';
        $update["_94LIST_ANNOUNCE"]            = '"' . htmlspecialchars(str_replace("\n", "[NextLine]", $request["announce"]), ENT_QUOTES) . '"';
        $update["_94LIST_NEED_INV_CODE"]       = $request["need_inv_code"];
        $update["_94LIST_WHITELIST_MODE"]      = $request["whitelist_mode"];
        $update["APP_DEBUG"]                   = $request["debug"];
        $update["APP_NAME"]                    = '"' . $request["name"] . '"';
        $update["_94LIST_MAIN_SERVER"]         = '"' . rtrim($request["main_server"], '/') . '"';
        $update["_94LIST_CODE"]                = '"' . $request["code"] . '"';
        $update["_94LIST_SHOW_COPYRIGHT"]      = $request["show_copyright"];
        $update["_94LIST_PARSE_MODE"]          = $request["parse_mode"];
        $update["_94LIST_MAX_FILESIZE"]        = $request["max_filesize"];
        $update["_94LIST_CUSTOM_COPYRIGHT"]    = '"' . $request["custom_copyright"] . '"';
        $update["_94LIST_MIN_SINGLE_FILESIZE"] = $request["min_single_filesize"];
        $update["_94LIST_MAX_SINGLE_FILESIZE"] = $request["max_single_filesize"];
        $update["_94LIST_TOKEN_MODE"]          = $request["token_mode"];
        $update["_94LIST_BUTTON_LINK"]         = '"' . $request["button_link"] . '"';
        $update["_94LIST_LIMIT_CN"]            = $request["limit_cn"];
        $update["_94LIST_LIMIT_PROV"]          = $request["limit_prov"];
        $update["_94LIST_SHOW_LOGIN_BUTTON"]   = $request["show_login_button"];
        $update["_94LIST_TOKEN_BIND_IP"]       = $request["token_bind_ip"];
        $update["_94LIST_USER_AGENT"]          = '"' . $request["user_agent"] . '"';
        $update["HKLIST_PROXY_SERVER"]         = '"' . $request["proxy_server"] . '"';
        $update["HKLIST_PROXY_PASSWORD"]       = '"' . $request["proxy_password"] . '"';

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
