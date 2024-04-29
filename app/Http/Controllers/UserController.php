<?php

namespace App\Http\Controllers;

use App\Models\Group;
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

        $user = User::query()->firstWhere('username', $request['username']);
        if (!$user) return ResponseController::accountNotExists();
        if (!Hash::check($request['password'], $user['password'])) return ResponseController::accountPasswordError();
        if (!Auth::attempt(['username' => $request['username'], 'password' => $request['password']])) return ResponseController::accountPasswordError();

        $request->session()->regenerate();
        return ResponseController::success(['role' => $user['role']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::accountExists();

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
        if (!Auth::user()) return ResponseController::userNotLogin();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return ResponseController::success();
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'group_id' => 'required|numeric',
            'role'     => ['required', Rule::in(['user', 'admin'])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::accountExists();

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

    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'       => 'required|numeric',
            'group_id' => 'numeric',
            'username' => 'string',
            'password' => 'string',
            'role'     => Rule::in(['user', 'admin'])
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->find($request['id']);
        if (!$user) return ResponseController::accountNotExists();

        $update = [];

        if ($request['group_id']) {
            $group = Group::query()->find($request['group_id']);
            if (!$group) return ResponseController::groupNotExists();
            $update['group_id'] = $request['group_id'];
        }

        if ($request['username']) {
            if (User::query()->firstWhere('username', $request['username'])) return ResponseController::accountExists();
            $update['username'] = $request['username'];
        }

        if ($request['password']) $update['password'] = Hash::make($request['password']);

        if ($request['role']) {
            if (!in_array($request['role'], ['admin', 'user'])) return ResponseController::roleNotExists();
            $update['role'] = $request['role'];
        }

        $user->update($update);
        return ResponseController::success();
    }

    public function removeAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->find($request['id']);
        if (!$user) return ResponseController::accountNotExists();

        $user->delete();
        return ResponseController::success();
    }
}
