<?php

namespace App\Http\Controllers;

use App\Models\BdUser;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function view()
    {
        if (Auth::check()) {
            return view("pages.admin");
        } else {
            return view("pages.login");
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "1");
        }

        if (Auth::attempt([
            'username' => $request['username'],
            'password' => $request['password'],
        ])) {
            $request->session()->regenerate();
            return ResponseController::response(200, "success");
        } else {
            return ResponseController::response(400, "failed");
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return ResponseController::response(200, "success");
    }

    public function changeUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nowPassword'     => 'required',
            'newPassword'     => 'required',
            'confirmPassword' => 'required',
            'newUsername'     => 'unique:users,username'
        ]);

        $user = User::find(Auth::user()->id);

        // 如果没有传入默认为当前用户名
        $request['newUsername'] = $request['newUsername'] ?? $user['username'];

        if ($validator->fails() && $user['username'] !== $request['newUsername']) {
            return ResponseController::response(400, "用户名已存在");
        }

        if (!Hash::check($request['nowPassword'], $user['password'])) {
            return ResponseController::response(400, "密码不正确");
        }

        if ($request['newPassword'] != $request['confirmPassword']) {
            return ResponseController::response(400, "failed");
        }

        $user->username = $request['newUsername'];
        $user->password = Hash::make($request['newPassword']);
        $user->save();

        $this->logout($request);

        return ResponseController::response(200, "success");
    }

    static public function modifyEnv(array $data)
    {
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use ($data) {
            foreach ($data as $key => $value) {
                if (str_contains($item, $key)) {
                    return $key . '=' . $value;
                }
            }

            return $item;
        });

        $content = implode("\n", $contentArray->toArray());

        File::put($envPath, $content);
    }

    public function changeConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sleep'          => 'required',
            'max_once'       => 'required',
            'user_agent'     => 'required',
            'announce'       => 'required',
            'announceSwitch' => 'required|boolean',
            'cookie'         => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "failed");
        }

        $this->modifyEnv([
            "_94LIST_UA"             => $request['user_agent'],
            "_94LIST_ANNOUNCESWITCH" => $request['announceSwitch'] ? 1 : 0,
            "_94LIST_ANNOUNCE"       => $request['announce'],
            "_94LIST_COOKIE"         => '"' . $request['cookie'] . '"',
            "_94LIST_SLEEP"          => $request['sleep'],
            "_94LIST_MAXONCE"        => $request['max_once']
        ]);

        return ResponseController::response(200, "success");
    }

    public function _getAccountInfo($cookie)
    {
        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.user_agent"),
                'cookie'     => $cookie
            ]
        ]);

        try {
            $response = $http->get("https://pan.baidu.com/rest/2.0/xpan/nas?method=uinfo");
        } catch (GuzzleException $e) {
            return [
                'type'  => 'failed',
                'data'  => json_decode($e->getResponse()->getBody()->getContents(), true),
                'error' => $e
            ];
        }

        return [
            'type' => 'success',
            'data' => json_decode($response->getBody()->getContents(), true)
        ];
    }

    public function getAccountInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cookie' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "failed");
        }

        $response = $this->_getAccountInfo($request['cookie']);

        if ($response['type'] === 'failed') {
            $e     = $response['error'];
            $error = $response['data'];
            if ($e->getCode() === 0) {
                return ResponseController::response(500, $e->getMessage(), $e);
            }

            if ($e->hasResponse()) {
                return ResponseController::response(500, $error['errmsg'], $error);
            } else {
                return ResponseController::response(500, "failed");
            }
        }
        return ResponseController::response(200, 'success', $response['data']);
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cookie' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "failed");
        }

        $accountInfo = $this->_getAccountInfo(['cookie' => $request['cookie']]);
        if ($accountInfo['type'] === 'failed') {
            return ResponseController::response(400, '获取账户信息失败');
        }

        $accountInfo = $accountInfo['data'];

        switch ($accountInfo['vip_type']) {
            case 0:
                $accountInfo['vip_type'] = '普通用户';
                break;
            case 1:
                $accountInfo['vip_type'] = '普通会员';
                break;
            case 2:
                $accountInfo['vip_type'] = '超级会员';
                break;
        }

        // 判断账号是否已经存在
        if (BdUser::query()->where('baidu_name', $accountInfo['baidu_name'])->exists()) {
            return ResponseController::response(400, '账号已存在');
        }

        BdUser::query()->insert([
            'netdisk_name' => $accountInfo['netdisk_name'],
            'baidu_name'   => $accountInfo['baidu_name'],
            'cookie'       => $request['cookie'],
            'add_time'     => date("Y-m-d H:i:s"),
            'use'          => date("Y-m-d H:i:s"),
            'state'        => "未使用",
            'switch'       => 1,
            'vip_type'     => $accountInfo['vip_type']
        ]);

        return ResponseController::response(200, 'success');
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "failed");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $account->delete();

        return ResponseController::response(200, 'success');
    }

    public function switchAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "failed");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $account->switch = $account->switch == 1 ? 0 : 1;
        $account->save();

        return ResponseController::response(200, 'success');
    }

    public function getAccounts(Request $request)
    {
        $size  = $request['size'] ?? 10;
        $users = BdUser::query()->paginate($size);
        return ResponseController::response(200, 'success', $users);
    }
}
