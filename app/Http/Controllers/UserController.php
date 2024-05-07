<?php

namespace App\Http\Controllers;

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
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (!Auth::attempt(['username' => $request['username'], 'password' => $request['password']])) return ResponseController::userPasswordError();

        $request->session()->regenerate();

        return ResponseController::success(['role' => Auth::user()['role']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (config("94list.need_inv_code")) {
            $validator = Validator::make($request->all(), [
                'inv_code' => 'required|string'
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $invCode = InvCode::query()->firstWhere('name', $request['inv_code']);
            if (!$invCode) return ResponseController::InvCodeNotExists();

            if ($invCode['use_count'] === $invCode['can_count']) return ResponseController::InvCodeNotExists();

            $invCode->increment('use_count');
        }

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::userExists();

        User::query()->create([
            'username'    => $request['username'],
            'password'    => Hash::make($request['password']),
            'role'        => 'user',
            'group_id'    => 0,
            'inv_code_id' => isset($invCode) ? $invCode['id'] : 0
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

    public function getUser(Request $request, $user_id = null)
    {
        if ($user_id !== null) {
            $user = User::query()->find($user_id);
            if (!$user) return ResponseController::userNotExists();
            return ResponseController::success($user);
        }

        $users = User::query()->paginate($request['size']);
        return ResponseController::success($users);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'group_id' => 'nullable|numeric',
            'role'     => ['required', Rule::in(['admin', 'user'])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::userExists();

        if ($request['group_id'] !== null) {
            $group = Group::query()->find($request['group_id']);
            if (!$group) return ResponseController::groupNotExists();
        }

        User::query()->create([
            'username'    => $request['username'],
            'password'    => Hash::make($request['password']),
            'role'        => $request['role'],
            'group_id'    => $request['group_id'] ?? 0,
            'inv_code_id' => -1
        ]);

        return ResponseController::success();
    }

    public function updateUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'group_id' => 'nullable|numeric',
            'role'     => ['nullable', Rule::in(['admin', 'user'])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->find($user_id);
        if (!$user) return ResponseController::userNotExists();

        $update = [];

        if ($request['group_id'] !== null) {
            if (!Group::query()->find($request['group_id'])) return ResponseController::groupNotExists();
            $update['group_id'] = $request['group_id'];
        }

        if ($request['username'] !== null) {
            $User = User::query()->firstWhere('username', $request['username']);
            if ($user['id'] !== $User['id']) return ResponseController::userExists();
            $update['username'] = $request['username'];
        }

        if ($request['password'] !== null && !Hash::isHashed($request['password'])) $update['password'] = Hash::make($request['password']);
        if ($request['role'] !== null) $update['role'] = $request['role'];

        if (count($update) === 0) return ResponseController::paramsError();

        $user->update($update);

        return ResponseController::success();
    }

    public function removeUser(Request $request, $user_id)
    {
        $user = User::query()->find($user_id);
        if (!$user) return ResponseController::userNotExists();

        $user->delete();

        return ResponseController::success();
    }
}
