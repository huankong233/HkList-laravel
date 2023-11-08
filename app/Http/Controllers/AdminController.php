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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        if (Auth::attempt([
            'username' => $request['username'],
            'password' => $request['password'],
        ])) {
            $request->session()->regenerate();
            return ResponseController::response(200, "登陆成功");
        } else {
            return ResponseController::response(400, "登陆失败,用户名或密码错误");
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return ResponseController::response(200, "退出登陆成功");
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
            return ResponseController::response(400, "两次密码不一致");
        }

        $user->username = $request['newUsername'];
        $user->password = Hash::make($request['newPassword']);
        $user->save();

        $this->logout($request);

        return ResponseController::response(200, "修改信息成功");
    }

    static public function modifyEnv(array $data, $envPath = null)
    {
        if ($envPath === null) $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use ($data) {
            foreach ($data as $key => $value) {
                if (str_starts_with($item, $key . '=')) {
                    return $key . '=' . $value;
                }
            }

            return $item;
        });

        $content = implode("\n", $contentArray->toArray());

        File::put($envPath, $content);
    }

    public function getConfig()
    {
        $config = config("94list");
        return ResponseController::response(200, "获取成功", [
            ...$config,
            "announce" => str_replace("[NextLine]", "\n", $config['announce']),
            "sleep"    => (int)$config['sleep'],
            'maxOnce'  => (int)$config['maxOnce'],
            "debug"    => config("app.debug")
        ]);
    }

    public function changeConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sleep'          => 'required',
            'maxOnce'        => 'required',
            'userAgent'      => 'required',
            'announceSwitch' => 'required|boolean',
            'cookie'         => 'required',
            'debug'          => 'required',
            'ssl'            => 'required',
            'prefix'         => 'required',
            'passwordSwitch' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        if ($request['announceSwitch']) {
            if (!isset($request['announce'])) return ResponseController::response(400, "参数错误");
        } else {
            $request['announceSwitch'] = config("94list.announceSwitch");
        }

        if ($request['passwordSwitch']) {
            if (!isset($request['password'])) return ResponseController::response(400, "参数错误");
        } else {
            $request['password'] = config("94list.password");
        }

        $this->modifyEnv([
            "APP_DEBUG"              => $request['debug'] ? 'true' : 'false',
            "_94LIST_UA"             => $request['userAgent'],
            "_94LIST_ANNOUNCESWITCH" => $request['announceSwitch'] ? 'true' : 'false',
            "_94LIST_ANNOUNCE"       => '"' . htmlspecialchars(str_replace("\n", "[NextLine]", $request['announce']), ENT_QUOTES) . '"',
            "_94LIST_COOKIE"         => '"' . $request['cookie'] . '"',
            "_94LIST_SLEEP"          => $request['sleep'],
            "_94LIST_MAXONCE"        => $request['maxOnce'],
            "APP_SSL"                => $request['ssl'] ? 'true' : 'false',
            "ADMIN_ROUTE_PREFIX"     => $request['prefix'],
            "_94LIST_PASSWORDSWITCH" => $request['passwordSwitch'] ? 'true' : 'false',
            "_94LIST_PASSWORD"       => $request['password']
        ]);

        return ResponseController::response(200, "修改配置成功");
    }

    public function _getAccountInfo($cookie)
    {
        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.userAgent"),
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

    public function _getSvipEndTime($cookie)
    {
        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.userAgent"),
                'Cookie'     => $cookie
            ]
        ]);

        try {
            $response = $http->get('https://pan.baidu.com/rest/2.0/membership/user?method=query&clienttype=0&app_id=250528&web=1');
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
            return ResponseController::response(400, "参数错误");
        }

        $response = $this->_getAccountInfo($request['cookie']);

        if ($response['type'] === 'failed') {
            $e     = $response['error'];
            $error = $response['data'];

            if ($e->getCode() === 0) {
                return ResponseController::response(500, $e->getMessage());
            }

            if ($e->hasResponse()) {
                if ($request['check']) {
                    if ($error['errmsg'] === "Invalid Bduss") {
                        return ResponseController::response(200, "cookie校验成功");
                    } else {
                        return ResponseController::response(500, $error['errmsg']);
                    }
                } else {
                    return ResponseController::response(500, $error['errmsg']);
                }
            } else {
                return ResponseController::response(500, $e->getMessage());
            }
        }

        if ($response['data']['vip_type'] === 2) {
            $svipEndTime = $this->_getSvipEndTime($request['cookie'])['data'];

            return ResponseController::response(200, 'success', [
                'svipEndTime' => $svipEndTime['currenttime'] + $svipEndTime['reminder']['svip']['leftseconds'],
                ...$response['data']
            ]);
        }

        return ResponseController::response(200, 'success', $response['data']);
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cookie' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        $accountInfo = $this->_getAccountInfo($request['cookie']);

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

        $svip_end_time = date("Y-m-d H:i:s");

        if ($accountInfo['vip_type'] === '超级会员') {
            $svipEndTime = $this->_getSvipEndTime($request['cookie']);

            if ($svipEndTime['type'] === 'failed') {
                return ResponseController::response(400, '获取SVIP到期时间失败');
            }

            $svipEndTime   = $svipEndTime['data'];
            $svip_end_time = date("Y-m-d H:i:s", $svipEndTime['currenttime'] + $svipEndTime['reminder']['svip']['leftseconds']);
        }

        BdUser::query()->insert([
            'netdisk_name'  => $accountInfo['netdisk_name'],
            'baidu_name'    => $accountInfo['baidu_name'],
            'cookie'        => $request['cookie'],
            'add_time'      => date("Y-m-d H:i:s"),
            'svip_end_time' => $svip_end_time,
            'use'           => date("Y-m-d H:i:s"),
            'state'         => "未使用",
            'switch'        => 1,
            'vip_type'      => $accountInfo['vip_type']
        ]);

        return ResponseController::response(200, '添加账号成功');
    }

    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $cookie = $account['cookie'];

        $accountInfo = $this->_getAccountInfo($cookie);

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

        $svip_end_time = date("Y-m-d H:i:s");

        if ($accountInfo['vip_type'] === '超级会员') {
            $svipEndTime = $this->_getSvipEndTime($cookie);

            if ($svipEndTime['type'] === 'failed') {
                return ResponseController::response(400, '获取SVIP到期时间失败');
            }

            $svipEndTime   = $svipEndTime['data'];
            $svip_end_time = date("Y-m-d H:i:s", $svipEndTime['currenttime'] + $svipEndTime['reminder']['svip']['leftseconds']);
        }

        $account->update([
            'netdisk_name'  => $accountInfo['netdisk_name'],
            'baidu_name'    => $accountInfo['baidu_name'],
            'svip_end_time' => $svip_end_time,
            'state'         => "未使用",
            'switch'        => 1,
            'vip_type'      => $accountInfo['vip_type']
        ]);

        return ResponseController::response(200, '更新账号信息成功');
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $account->delete();

        return ResponseController::response(200, '删除账号成功');
    }

    public function switchAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $account->switch = $account['switch'] === 1 ? 0 : 1;
        if ($account['state'] !== "未使用" && $account['state'] !== '会员过期') {
            if ($account['switch'] === 1) {
                $account->state = "能用";
            } else if ($account['switch'] === 0 && $account['state'] === "死亡") {
                $account->state = "死亡";
            }
        }

        $account->save();

        return ResponseController::response(200, '切换成功');
    }

    public function getAccounts(Request $request)
    {
        $size  = $request['size'] ?? 10;
        $users = BdUser::query()->paginate($size);
        return ResponseController::response(200, '获取成功', $users);
    }
}
