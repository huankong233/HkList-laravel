<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function getTokens(Request $request)
    {
        $tokens = Token::query()
                       ->withCount([
                           'records as total_count',
                           'records as today_count' => function ($query) {
                               $query->whereDate('created_at', Carbon::today(config("app.timezone")));
                           }
                       ])
                       ->withSum([
                           'records as total_size' => function ($query) {
                               $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id');
                           },
                           'records as today_size' => function ($query) {
                               $query->leftJoin('file_lists', 'file_lists.id', '=', 'records.fs_id')
                                     ->whereDate('records.created_at', Carbon::today(config("app.timezone")));
                           }
                       ], "file_lists.size")
                       ->paginate($request["size"]);
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

        $token = Token::query()->firstWhere("name", $request["name"]);
        if ($token) return ResponseController::TokenExists();

        Token::query()->create([
            "name"  => $request["name"],
            "count" => $request["count"],
            "size"  => $request["size"],
            "day"   => $request["day"],
            "ip"    => null
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
            $name  = Str::random();
            $token = Token::query()->firstWhere("name", $name);
            if ($token) {
                $i--;
                continue;
            }

            Token::query()->create([
                "name"  => $name,
                "count" => $request["count"],
                "size"  => $request["size"],
                "day"   => $request["day"],
                "ip"    => null
            ]);
        }

        return ResponseController::success();
    }

    public function updateToken(Request $request, $token_id)
    {
        $validator = Validator::make($request->all(), [
            "name"       => "required|string",
            "count"      => "required|numeric",
            "size"       => "required|numeric",
            "day"        => "required|numeric",
            "expired_at" => "nullable|date",
            "ip"         => "nullable|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $token = Token::query()->find($token_id);
        if (!$token) return ResponseController::TokenNotExists();

        $Token = Token::query()->firstWhere("name", $request["name"]);
        if ($Token && $token["id"] !== $Token["id"]) return ResponseController::TokenExists();

        $token->update([
            "name"       => $request["name"],
            "count"      => $request["count"],
            "size"       => $request["size"],
            "day"        => $request["day"],
            "expired_at" => $request["expired_at"] ? Carbon::createFromTimeString($request["expired_at"], config("app.timezone"))->addHours(8)->format("Y-m-d H:i:s") : null,
            "ip"         => $request["ip"] === "" ? null : $request["ip"]
        ]);

        return ResponseController::success();
    }

    public function removeTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token_ids"   => "required|array",
            "token_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Token::query()->whereIn("id", $request["token_ids"])->delete();

        return ResponseController::success();
    }
}
