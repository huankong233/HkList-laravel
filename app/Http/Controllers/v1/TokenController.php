<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            "size"  => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $Token = Token::query()->firstWhere("name", $request["name"]);
        if ($Token) return ResponseController::TokenExists();

        Token::query()->create([
            "name"  => $request["name"],
            "count" => $request["count"],
            "size"  => $request["size"]
        ]);

        return ResponseController::success();
    }

    public function updateToken(Request $request, $Token_id)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "string",
            "count" => "numeric",
            "size"  => "numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $Token = Token::query()->find($Token_id);
        if (!$Token) return ResponseController::TokenNotExists();

        $update = [];

        if (isset($request["name"])) {
            $Token = Token::query()->firstWhere("name", $request["name"]);
            if ($Token && $Token["id"] !== $Token["id"]) return ResponseController::TokenExists();
            $update["name"] = $request["name"];
        }

        if (isset($request["count"])) $update["count"] = $request["count"];
        if (isset($request["size"])) $update["size"] = $request["size"];

        if (count($update) === 0) return ResponseController::paramsError();

        $Token->update($update);

        return ResponseController::success();
    }

    public function removeTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "Token_ids.*" => "numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request["Token_ids"] as $Token_id) {
            if (in_array($Token_id, ["-1", "0"])) return ResponseController::TokenCanNotBeRemoved("自带分组禁止删除");

            $Token = Token::query()->find($Token_id);
            if (!$Token) return ResponseController::TokenNotExists();

            $users = User::query()->where("Token_id", $Token_id)->get();
            if ($users->count() > 0) return ResponseController::TokenCanNotBeRemoved("用户组还存在用户");

            $Token->delete();
        }

        return ResponseController::success();
    }
}
