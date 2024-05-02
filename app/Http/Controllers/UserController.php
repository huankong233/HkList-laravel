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
                'invCode' => 'required|string'
            ]);

            if ($validator->fails()) return ResponseController::paramsError();

            $invCode = InvCode::query()->firstWhere('name', $request['invCode']);
            if (!$invCode) return ResponseController::InvCodeNotExists();

            if ($invCode['use_count'] === $invCode['can_count']) return ResponseController::InvCodeNotExists();

            $invCode->increment('use_count');
        }

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::userExists();

        User::query()->create([
            'username' => $request['username'],
            'password' => Hash::make($request['password']),
            'role'     => 'user',
            'group_id' => 0
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

        $users = User::query()->get();
        return ResponseController::success($users);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'group_id' => 'required|numeric',
            'role'     => ['required', Rule::in(['admin', 'user'])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::userExists();

        $group = Group::query()->find($request['group_id']);
        if (!$group) return ResponseController::groupNotExists();

        User::query()->create([
            'username' => $request['username'],
            'password' => Hash::make($request['password']),
            'role'     => $request['role'],
            'group_id' => $request['group_id']
        ]);

        return ResponseController::success();
    }

    public function updateUser(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'numeric',
            'username' => 'string',
            'password' => 'string',
            'role'     => Rule::in(['admin', 'user'])
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->find($user_id);
        if (!$user) return ResponseController::userNotExists();

        $update = [];

        if ($request['group_id']) {
            if (!Group::query()->find($request['group_id'])) return ResponseController::groupNotExists();
            $update['group_id'] = $request['group_id'];
        }

        if ($request['username']) {
            if (User::query()->firstWhere('username', $request['username'])) return ResponseController::userExists();
            $update['username'] = $request['username'];
        }

        if ($request['password']) $update['password'] = Hash::make($request['password']);
        if ($request['role']) $update['role'] = $request['role'];

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
