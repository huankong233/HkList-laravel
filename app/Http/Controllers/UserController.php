<?php

namespace App\Http\Controllers;

use App\Models\BdUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function view()
    {
        return view("pages.user");
    }

    public function getRandomCookie($vipType = ["超级会员"])
    {
        return BdUser::query()
                     ->where([
                         'switch' => '1'
                     ])
                     ->where(function (Builder $query) use ($vipType) {
                         foreach ($vipType as $item) {
                             $query->orWhere("vip_type", $item);
                         }
                     })
                     ->where('state', '!=', '死亡')
                     ->orderByRaw("RAND()")
                     ->first();
    }

    public function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        if (getenv('HTTP_X_REAL_IP')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip  = getenv('HTTP_X_FORWARDED_FOR');
            $ips = explode(',', $ip);
            $ip  = $ips[0];
        } else if (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = '0.0.0.0';
        }

        return $ip;
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, 'failed');
        }

        preg_match(strpos($request['url'], '/surl') ? "/surl=([a-zA-Z0-9_-]+)/" : "/s\/([a-zA-Z0-9_-]+)/", $request['url'], $shortUrl);
        if (!$shortUrl) {
            return ResponseController::response(400, 'url格式错误');
        } else {
            $shortUrl = $shortUrl[1];
        }

        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.user_agent"),
                'cookie'     => config("94list.cookie")
            ]
        ]);

        $requestData = [
            'shorturl' => $shortUrl,
            'dir'      => $request['dir'] ?? null,
            'root'     => $request['dir'] === '' || $request['dir'] === null || $request['dir'] === '/' ? 1 : 0,
            'pwd'      => $request['password'] ?? '',
            'page'     => $request['page'] ?? 1,
            'num'      => $request['num'] ?? 1000,
            'order'    => $request['order'] ?? 'filename'
        ];

        try {
            $response = $http->post("https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1&qq-pf-to=pcqq.c2c", [
                'form_params' => $requestData
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
                'list'    => $contents['data']['list'],
            ]),
            9019 => ResponseController::response(400, "代理账号出现问题,请重试", $requestData),
            default => ResponseController::response(400, "异常错误:" . $contents['errno'] . ",可能链接已失效或是未提供正确的密码", $requestData),
        };
    }

    public function getSign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uk'      => 'required',
            'shareid' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseController::response(400, 'failed');
        }

        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.user_agent"),
                'cookie'     => config("94list.cookie")
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
            9019 => ResponseController::response(400, "获取信息的代理账号有问题"),
            default => ResponseController::response(400, "异常错误:" . $contents['errno'] . ",获取签名信息失败"),
        };

    }
}
