<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'group'    => 0
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
            'group_id' => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $user = User::query()->firstWhere('username', $request['username']);
        if ($user) return ResponseController::accountExists();

        User::query()->create([
            'username' => $request['username'],
            'password' => Hash::make($request['password']),
            'role'     => 'user',
            'group_id' => $request['group_id']
        ]);

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
