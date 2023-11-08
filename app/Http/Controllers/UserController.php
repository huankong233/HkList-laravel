<?php

namespace App\Http\Controllers;

use App\Models\BdUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function view(Request $request)
    {
        return view("main");
    }

    public function getConfig()
    {
        $config = config("94list");

        return ResponseController::response(200, "获取成功", [
            'announce'       => $config['announce'],
            'announceSwitch' => $config['announceSwitch'],
            'userAgent'      => $config['userAgent'],
            "debug"          => config("app.debug"),
            "haveAccount"    => $this->getRandomCookie() !== null,
            'havePassword'   => $config['passwordSwitch'],
            "haveLogin"      => Auth::check()
        ]);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, '参数错误');
        }

        $user          = Auth::user();
        $checkPassword = self::checkPassword($user, $request['password']);
        if ($checkPassword) return $checkPassword;

        preg_match("/s\/([a-zA-Z0-9_-]+)/", trim($request['url']), $shortUrl);
        if (!$shortUrl) {
            return ResponseController::response(400, 'url格式错误');
        } else {
            $shortUrl = $shortUrl[1];
        }

        $http = new Client([
            'headers' => [
                'Cookie' => config("94list.cookie")
            ]
        ]);

        try {
            $response = $http->post("https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1&qq-pf-to=pcqq.c2c", [
                'form_params' => [
                    'shorturl' => $shortUrl,
                    'dir'      => $request['dir'] ?? null,
                    'root'     => $request['dir'] === '' || $request['dir'] === null || $request['dir'] === '/' ? 1 : 0,
                    'pwd'      => trim($request['pwd']) ?? '',
                    'page'     => $request['page'] ?? 1,
                    'num'      => $request['num'] ?? 9999,
                    'order'    => $request['order'] ?? 'filename'
                ]
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $contents = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return match ($contents['errno']) {
            0 => ResponseController::response(200, '列表数据获取成功', [
                'uk'      => $contents["data"]["uk"],
                'shareid' => $contents["data"]["shareid"],
                'randsk'  => $contents["data"]["seckey"],
                'list'    => $contents['data']['list']
            ]),
            9019 => ResponseController::response(400, "获取列表的Cookie失效"),
            default => ResponseController::response(400, "异常错误:" . $contents['errno'] . ",可能链接已失效或是未提供正确的密码"),
        };
    }

    public function getSign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uk'      => 'required',
            'shareid' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, '参数错误');
        }

        $user          = Auth::user();
        $checkPassword = self::checkPassword($user, $request['password']);
        if ($checkPassword) return $checkPassword;

        $http = new Client([
            'headers' => [
                'Cookie' => config("94list.cookie")
            ]
        ]);

        try {
            $response = $http->get('https://pan.baidu.com/share/tplconfig', [
                'query' => [
                    'shareid'    => $request['shareid'],
                    'uk'         => $request['uk'],
                    'fields'     => 'sign,timestamp',
                    'channel'    => 'chunlei',
                    'web'        => 1,
                    'app_id'     => 250528,
                    'clienttype' => 0
                ]
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $contents = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return match ($contents['errno']) {
            0 => ResponseController::response(200, "获取签名成功", $contents['data']),
            9019 => ResponseController::response(400, "获取列表签名的Cookie失效"),
            default => ResponseController::response(400, "异常错误:" . $contents['errno'] . ",获取签名信息失败"),
        };
    }

    static public function getRandomCookie($vipType = ["超级会员"])
    {
        // 禁用不可用的账户
        BdUser::query()
              ->where('switch', '=', '1')
              ->where('state', '!=', '死亡')
              ->where('state', '!=', '会员过期')
              ->where(function (Builder $query) use ($vipType) {
                  foreach ($vipType as $item) {
                      $query->orWhere("vip_type", $item);
                  }
              })
              ->whereRaw(
                  config("database.default") === 'sqlite'
                      ? "\"svip_end_time\" < DATETIME('now', 'utc', '+16 hours')"
                      : "`svip_end_time` < convert_tz(UTC_TIMESTAMP(), '+00:00', '+08:00')"
              )
              ->update([
                  'switch' => '0',
                  'state'  => '会员过期'
              ]);

        return BdUser::query()
                     ->where('switch', '=', '1')
                     ->where('state', '!=', '死亡')
                     ->where('state', '!=', '会员过期')
                     ->where(function (Builder $query) use ($vipType) {
                         foreach ($vipType as $item) {
                             $query->orWhere("vip_type", $item);
                         }
                     })
                     ->orderByRaw(config("database.default") === 'sqlite' ? "RANDOM()" : "RAND()")
                     ->first();
    }

    static function checkPassword($user, $password)
    {
        // 判断是否启用了密码
        if (config("94list.passwordSwitch")) {
            // 如果是管理员就跳过
            // 未登陆或不是管理员
            if (!$user || !$user['is_admin']) {
                if (!$password) {
                    return ResponseController::response(400, '请提供密码!');
                }

                if ($password !== config("94list.password")) {
                    return ResponseController::response(400, '密码不匹配');
                }
            }
        }

        return false;
    }

    public function downloadFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fs_ids'    => 'required',
            'timestamp' => 'required',
            'uk'        => 'required',
            'sign'      => 'required',
            'randsk'    => 'required',
            'shareid'   => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, '参数错误');
        }

        if (count($request['fs_ids']) > config("94list.maxOnce")) {
            return ResponseController::response(400, '超出单次解析最大数量');
        }

        $user = Auth::user();

        $checkPassword = self::checkPassword($user, $request['password']);
        if ($checkPassword) return $checkPassword;

        // 判断是否指定了某个账户
        $cookieId = $request['bd_user_id'];
        if (isset($cookieId)) {
            if ($user && $user['is_admin']) {
                $cookie = BdUser::query()->find($cookieId);
                if ($cookie === null) {
                    return ResponseController::response(400, '代理账号不存在');
                }
            } else {
                return ResponseController::response(400, '您没有权限指定下载的用户');
            }
        } else {
            $cookie = $this->getRandomCookie();
            if ($cookie === null) {
                return ResponseController::response(400, '代理账号已用完');
            }
        }

        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.userAgent"),
                'Cookie'     => $cookie['cookie'],
                "Referer"    => "https://pan.baidu.com/disk/home",
                "Host"       => "pan.baidu.com",
            ]
        ]);

        try {
            $response = $http->post('https://pan.baidu.com/api/sharedownload', [
                'query' => [
                    "channel"    => "chunlei",
                    "clienttype" => 12,
                    "sign"       => $request['sign'],
                    "timestamp"  => $request['timestamp'],
                    "web"        => 1
                ],
                "body"  => join("&", [
                    "encrypt=0",
                    "extra=" . urlencode('{"sekey":"' . urldecode($request['randsk']) . '"}'),
                    "fid_list=[" . join(",", $request['fs_ids']) . "]",
                    "primaryid=" . $request['shareid'],
                    "uk=" . $request["uk"],
                    "product=share",
                    "type=nolimit"
                ])
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $contents = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        switch ($contents["errno"]) {
            case 0:
                $cookie->state = "能用";
                $cookie->use   = date("Y-m-d H:i:s");
                $cookie->save();

                // 如果就一个文件就不睡
                // 有多个文件就每个睡一觉
                $sleepTime    = count($contents['list']) > 1 ? config("94list.sleep") : 0;
                $responseData = [];

                foreach ($contents['list'] as $list) {
                    $dlink = $list['dlink'];
                    try {
                        $headResponse   = $http->head($dlink, [
                            'allow_redirects' => [
                                'follow_redirects' => false,
                                'track_redirects'  => true,
                            ]
                        ]);
                        $redirects      = $headResponse->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);
                        $effective_url  = end($redirects) ?: $headResponse->getEffectiveUri();
                        $responseData[] = [
                            'dlink'           => $effective_url,
                            'server_filename' => $list['server_filename']
                        ];
                    } catch (GuzzleException $e) {
                        $cookie->state  = "死亡";
                        $cookie->switch = 0;
                        $cookie->save();
                        return ResponseController::response(500, "账户可能被拉黑,账户id:" . $cookie['id'] . $e->getMessage());
                    }
                    sleep($sleepTime);
                }

                return ResponseController::response(200, '获取成功', $responseData);
            case 112:
                return ResponseController::response(400, "当前签名已过期,请刷新页面重新获取");
            case '9019':
            case '8001':
                $cookie->state  = "死亡";
                $cookie->switch = 0;
                $cookie->save();
                return ResponseController::response(400, "代理账号失效或者IP被封禁");
            case '110':
                return ResponseController::response(400, "当前代理ip被封禁");
            default:
                return ResponseController::response(400, "未知错误代码：" . $contents['errno']);
        }
    }
}
