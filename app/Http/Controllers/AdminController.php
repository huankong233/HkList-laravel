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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
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
            'nowPassword'     => 'required|string',
            'newPassword'     => 'required|string',
            'confirmPassword' => 'required|string',
            'newUsername'     => 'unique:users,username|string'
        ]);

        $user = User::query()->find(Auth::user()['id']);

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

        $user['username'] = $request['newUsername'];
        $user['password'] = Hash::make($request['newPassword']);
        $user->save();

        $this->logout($request);

        return ResponseController::response(200, "修改信息成功");
    }

    public static function modifyEnv(array $data, $envPath = null)
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
            'announce'       => 'string',
            'announceSwitch' => 'required|boolean',
            'cookie'         => 'required|string',
            'debug'          => 'required|boolean',
            'maxOnce'        => 'required|numeric',
            'password'       => 'string',
            'passwordSwitch' => 'required|boolean',
            'prefix'         => 'required|string',
            'sleep'          => 'required|numeric',
            'ssl'            => 'required|boolean',
            'userAgent'      => 'required|string',
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

    public function getMailConfig()
    {
        $config = config("mail");
        return ResponseController::response(200, "获取成功", [
            'mailSwitch'      => $config['switch'],
            'mailTo'          => $config['to'],
            'mailHost'        => $config['mailers']['smtp']['host'],
            'mailPort'        => $config['mailers']['smtp']['port'],
            'mailUsername'    => $config['mailers']['smtp']['username'],
            'mailPassword'    => $config['mailers']['smtp']['password'],
            'mailFromName'    => $config['from']['name'],
            'mailFromAddress' => $config['from']['address'],
        ]);
    }

    public function changeMailConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mailSwitch'      => 'required|boolean',
            'mailTo'          => 'string',
            'mailHost'        => 'string',
            'mailPort'        => 'string',
            'mailUsername'    => 'string',
            'mailPassword'    => 'string',
            'mailFromName'    => 'string',
            'mailFromAddress' => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        if (!$request['mailSwitch']) {
            $this->modifyEnv([
                "MAIL_SWITCH" => 'false',
            ]);

        } else {
            $validator = Validator::make($request->all(), [
                'mailTo'          => 'required',
                'mailHost'        => 'required',
                'mailPort'        => 'required',
                'mailUsername'    => 'required',
                'mailPassword'    => 'required',
                'mailFromName'    => 'required',
                'mailFromAddress' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseController::response(400, "参数错误");
            }

            $this->modifyEnv([
                "MAIL_SWITCH"       => $request['mailSwitch'] ? 'true' : 'false',
                "MAIL_TO"           => $request['mailTo'],
                "MAIL_HOST"         => $request['mailHost'],
                "MAIL_PORT"         => $request['mailPort'],
                "MAIL_USERNAME"     => $request['mailUsername'],
                "MAIL_PASSWORD"     => $request['mailPassword'],
                "MAIL_FROM_NAME"    => $request['mailFromName'],
                "MAIL_FROM_ADDRESS" => $request['mailFromAddress']
            ]);
        }

        return ResponseController::response(200, "修改配置成功");
    }

    public function sendTestMsg()
    {
        Mail::raw('测试邮件发送成功~', function ($message) {
            $to = config("mail.to");
            $message->to($to)->subject('这有一条测试邮件发出了哦~');
        });
        return ResponseController::response(200, "发送成功");
    }

    public static function _getAccountInfo($cookie)
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

    public static function _getSvipEndTime($cookie)
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
            'cookie' => 'required|string'
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
            'cookie' => 'required|string'
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

        $svip_end_time = date("Y-m-d H:i:s", 0);
        $switch        = 0;

        if ($accountInfo['vip_type'] === '超级会员') {
            $svipEndTime = $this->_getSvipEndTime($request['cookie']);

            if ($svipEndTime['type'] === 'failed') {
                return ResponseController::response(400, '获取SVIP到期时间失败');
            }

            $svipEndTime   = $svipEndTime['data'];
            $svip_end_time = date("Y-m-d H:i:s", $svipEndTime['currenttime'] + $svipEndTime['reminder']['svip']['leftseconds']);
            if ($svip_end_time > date("Y-m-d H:i:s")) {
                $switch = 1;
            }
        }

        BdUser::query()->insert([
            'netdisk_name'  => $accountInfo['netdisk_name'],
            'baidu_name'    => $accountInfo['baidu_name'],
            'cookie'        => $request['cookie'],
            'add_time'      => date("Y-m-d H:i:s"),
            'svip_end_time' => $svip_end_time,
            'use'           => date("Y-m-d H:i:s", 0),
            'state'         => $switch === 1 ? "未使用" : "会员过期",
            'switch'        => $switch,
            'vip_type'      => $accountInfo['vip_type']
        ]);

        return ResponseController::response(200, '添加账号成功');
    }

    public static function updateAccount(Request $request, $fromBan = false)
    {
        if ($fromBan === false) {
            $validator = Validator::make($request->all(), [
                'account_id' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return ResponseController::response(400, "参数错误");
            }

            $account = BdUser::query()->find($request['account_id']);

            if ($account == null) {
                return ResponseController::response(400, "账号不存在");
            }
        } else {
            $account = $fromBan;
        }

        $cookie = $account['cookie'];

        $accountInfo = AdminController::_getAccountInfo($cookie);

        if ($accountInfo['type'] === 'failed') {
            return $fromBan ? '获取账户信息失败' : ResponseController::response(400, '获取账户信息失败');
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

        $svip_end_time = date("Y-m-d H:i:s", 0);
        $switch        = 0;

        if ($accountInfo['vip_type'] === '超级会员') {
            $svipEndTime = AdminController::_getSvipEndTime($cookie);

            if ($svipEndTime['type'] === 'failed') {
                return $fromBan ? '获取SVIP到期时间失败' : ResponseController::response(400, '获取SVIP到期时间失败');
            }

            $svipEndTime   = $svipEndTime['data'];
            $svip_end_time = date("Y-m-d H:i:s", $svipEndTime['currenttime'] + $svipEndTime['reminder']['svip']['leftseconds']);
            if ($svip_end_time > date("Y-m-d H:i:s")) {
                $switch = 1;
            }
        }

        $account->update([
            'netdisk_name'  => $accountInfo['netdisk_name'],
            'baidu_name'    => $accountInfo['baidu_name'],
            'svip_end_time' => $svip_end_time,
            'state'         => $switch === 1 ? "未使用" : "会员过期",
            'switch'        => $switch,
            'vip_type'      => $accountInfo['vip_type']
        ]);

        return $fromBan ? '更新账号信息成功' : ResponseController::response(200, '更新账号信息成功');
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|numeric'
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
            'account_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, "参数错误");
        }

        $account = BdUser::query()->find($request['account_id']);

        if ($account == null) {
            return ResponseController::response(400, "账号不存在");
        }

        $account['switch'] = $account['switch'] === 1 ? 0 : 1;
        if ($account['state'] !== "未使用" && $account['state'] !== '会员过期') {
            if ($account['switch'] === 1) {
                $account['state'] = "能用";
            }
            if ($account['switch'] === 0 && $account['state'] === "死亡") {
                $account['state'] = "死亡";
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
