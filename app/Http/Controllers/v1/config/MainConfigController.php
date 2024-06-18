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
            "sleep"          => "numeric",
            "max_once"       => "numeric",
            "password"       => "nullable|string",
            "announce"       => "nullable|string",
            "user_agent"     => "string",
            "need_inv_code"  => "bool",
            "whitelist_mode" => "bool",
            "debug"          => "bool",
            "name"           => "string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if (isset($request["sleep"])) $update["_94LIST_SLEEP"] = $request["sleep"];
        if (isset($request["max_once"])) $update["_94LIST_MAX_ONCE"] = $request["max_once"];
        if (isset($request["password"])) $update["_94LIST_PASSWORD"] = $request["password"] === null ? "" : '"' . $request["password"] . '"';
        if (isset($request["announce"])) $update["_94LIST_ANNOUNCE"] = $request["announce"] === null ? "" : '"' . htmlspecialchars(str_replace("\n", "[NextLine]", $request["announce"]), ENT_QUOTES) . '"';
        if (isset($request["user_agent"])) $update["_94LIST_USER_AGENT"] = '"' . $request["user_agent"] . '"';
        if (isset($request["need_inv_code"])) $update["_94LIST_NEED_INV_CODE"] = $request["need_inv_code"];
        if (isset($request["whitelist_mode"])) $update["_94LIST_WHITELIST_MODE"] = $request["whitelist_mode"];
        if (isset($request["debug"])) $update["APP_DEBUG"] = $request["debug"];
        if (isset($request["name"])) $update["APP_NAME"] = $request["name"];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }

    public function testAuth(Request $request)
    {
        $updateConfig     = self::updateConfig($request);
        $updateConfigData = $updateConfig->getData(true);
        if ($updateConfigData["code"] !== 200) return $updateConfig;

        // 测试
        try {
            $http = new Client();
            $res  = $http->post(config("94list.main_server") . "/api/checkCode", ["json" => ["code" => config("94list.code")]]);
            return JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            return JSON::decode($e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            return ResponseController::networkError("连接解析服务器");
        }
    }
}
