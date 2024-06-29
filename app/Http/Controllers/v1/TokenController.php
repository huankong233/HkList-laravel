<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function getToken(Request $request, $token_id = null)
    {
        if ($token_id !== null) {
            $token = Token::query()->find($token_id);
            if (!$token) return ResponseController::TokenNotExists();
            return ResponseController::success($token);
        }

        $tokens = Token::query()->paginate($request["size"]);
        return ResponseController::success($tokens);
    }

    public function addToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "required|string",
            "count" => "required|numeric",
            "size"  => "required|numeric",
            "day"   => "required|numeric",
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $Token = Token::query()->firstWhere("name", $request["name"]);
        if ($Token) return ResponseController::TokenExists();

        Token::query()->create([
            "name"  => $request["name"],
            "count" => $request["count"],
            "size"  => $request["size"],
            "day"   => $request["day"]
        ]);

        return ResponseController::success();
    }

    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token_count" => "required|numeric",
            "count"       => "required|numeric",
            "size"        => "required|numeric",
            "day"         => "required|numeric",
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        for ($i = 0; $i < $request["token_count"]; $i++) {
            $name    = Str::random();
            $invCode = Token::query()->firstWhere("name", $name);
            if ($invCode) {
                $i--;
                continue;
            }

            $token = Token::query()->create([
                "name"  => $name,
                "count" => $request["count"],
                "size"  => $request["size"],
                "day"   => $request["day"]
            ]);

            // 傻逼Sqlite设置的自增起始值无效

            if ($token["id"] === 1) {
                $token->delete();
                return self::generateToken($request);
            }
        }

        return ResponseController::success();
    }

    public function updateToken(Request $request, $token_id)
    {
        $validator = Validator::make($request->all(), [
            "name"       => "string",
            "count"      => "numeric",
            "size"       => "numeric",
            "day"        => "numeric",
            "expired_at" => "nullable|date"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $token = Token::query()->find($token_id);
        if (!$token) return ResponseController::TokenNotExists();

        $update = [];

        if (isset($request["name"])) {
            $Token = Token::query()->firstWhere("name", $request["name"]);
            if ($Token && $token["id"] !== $Token["id"]) return ResponseController::TokenExists();
            $update["name"] = $request["name"];
        }

        if (isset($request["count"])) $update["count"] = $request["count"];
        if (isset($request["size"])) $update["size"] = $request["size"];
        if (isset($request["day"])) $update["day"] = $request["day"];
        if (isset($request["expired_at"])) $update["expired_at"] = $request["expired_at"];

        if (count($update) === 0) return ResponseController::paramsError();

        $token->update($update);

        return ResponseController::success();
    }

    public function removeTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token_ids"   => "required|array",
            "token_ids.*" => "numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request["token_ids"] as $token_id) {
            $token = Token::query()->find($token_id);
            if (!$token) return ResponseController::TokenNotExists();
            $token->delete();
        }

        return ResponseController::success();
    }
}
