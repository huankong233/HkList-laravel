<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\InvCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * inv_code:
 * -1 表示后端直接创建
 * 0 表示创建时不需要使用邀请码
 */
class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string",
            "password" => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (!Auth::attempt(["username" => $request["username"], "password" => $request["password"]])) return ResponseController::userPasswordError();

        $request->session()->regenerate();

        return ResponseController::success(["role" => Auth::user()["role"]]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string",
            "password" => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere("username", $request["username"]);
        if ($user) return ResponseController::userExists();

        if (config("94list.need_inv_code")) {
            $validator = Validator::make($request->all(), [
                "inv_code" => "required|string"
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $invCode = InvCode::query()->firstWhere("name", $request["inv_code"]);
            if (!$invCode) return ResponseController::InvCodeNotExists();

            // 获取 use_count
            $use_count = User::query()->where("inv_code_id", $invCode["id"])->count();

            if ($use_count >= $invCode["can_count"]) return ResponseController::invCodeQuotaHasBeenUsedUp();
        }

        User::query()->create([
            "username"    => $request["username"],
            "password"    => Hash::make($request["password"]),
            "role"        => "user",
            "inv_code_id" => isset($invCode) ? $invCode["id"] : 1
        ]);

        return ResponseController::success();
    }

    public function logout(Request $request)
    {
        if (!Auth::check()) return ResponseController::userNotLogin();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return ResponseController::success();
    }

    public function getUsers(Request $request)
    {
        $users = User::query()->paginate($request["size"]);
        return ResponseController::success($users);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username"    => "required|string",
            "password"    => "required|string",
            "inv_code_id" => "numeric",
            "role"        => ["required", Rule::in(["admin", "user"])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere("username", $request["username"]);
        if ($user) return ResponseController::userExists();

        if (isset($request["inv_code_id"])) {
            $invCode = InvCode::query()->find($request["inv_code_id"]);
            if (!$invCode) return ResponseController::invCodeNotExists();
        }

        User::query()->create([
            "username"    => $request["username"],
            "password"    => Hash::make($request["password"]),
            "role"        => $request["role"],
            "inv_code_id" => $request["inv_code_id"] ?? 1
        ]);

        return ResponseController::success();
    }

    public function updateUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            "username"    => "required|string",
            "password"    => "required|string",
            "inv_code_id" => "required|numeric",
            "role"        => ["required", Rule::in(["admin", "user"])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->find($user_id);
        if (!$user) return ResponseController::userNotExists();

        $User = User::query()->firstWhere("username", $request["username"]);
        if ($User && $user["id"] !== $User["id"]) return ResponseController::userExists();

        $user->update([
            "username" => $request["username"],
            "password" => Hash::isHashed($request["password"]) ? $request["password"] : Hash::make($request["password"]),
            "role"     => $request["role"],
        ]);

        return ResponseController::success();
    }

    public function removeUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_ids"   => "required|array",
            "user_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        User::query()->whereIn("id", $request["user_ids"])->delete();

        return ResponseController::success();
    }
}
