<?php

namespace App\Http\Controllers\v1\config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\ResponseController;
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
}
