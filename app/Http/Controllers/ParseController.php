<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Group;
use App\Models\Record;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ParseController extends Controller
{
    public function getConfig(Request $request)
    {
        $config = config('94list');

        $have_account = true;

        if (
            self::getRandomCookie($request)->getData(true)['data'] === null ||
            self::getRandomCookie($request, ['普通用户', '普通会员'])->getData(true)['data'] === null
        )
            $have_account = false;

        return ResponseController::success([
            'announce'      => $config['announce'],
            'user_agent'    => $config['user_agent'],
            'debug'         => config('app.debug'),
            'max_once'      => $config['max_once'],
            'have_account'  => $have_account,
            'have_login'    => Auth::check(),
            'need_inv_code' => $config['need_inv_code'],
            'need_password' => $config['password'] !== ''
        ]);
    }

    public static function getRandomCookie(Request $request, $vipType = '超级会员')
    {
        $vipType = is_array($vipType) ? $vipType : [$vipType];

        if (in_array('超级会员', $vipType)) {
            // 禁用不可用的账户
            $banAccounts = Account::query()
                                  ->where([
                                      'switch'   => 1,
                                      'vip_type' => '超级会员',
                                  ])
                                  ->whereDate('svip_end_at', '<', now())
                                  ->whereTime('svip_end_at', '<', now())
                                  ->get();

            $updateFailedAccounts = [];

            if ($banAccounts->count() !== 0) {
                // 更新账户状态
                foreach ($banAccounts as $account) {
                    $updateRes  = AccountController::updateAccount($request, $account['id']);
                    $updateData = $updateRes->getData(true);
                    if ($updateData['code'] !== 200) {
                        $account->update([
                            'switch' => 0,
                            'reason' => $updateData['message']
                        ]);
                        $updateFailedAccounts[] = $account->toJson();
                    }
                    sleep(1);
                }

                if (config('mail.switch')) {
                    Mail::raw('亲爱的' . config('mail.to.name') . ':\n\t有账户已过期,详见:' . json_encode($updateFailedAccounts), function ($message) {
                        $message->to(config('mail.to.address'))->subject('有账户过期了~');
                    });
                }
            }
        }

        $account = Account::query()
                          ->where('switch', 1)
                          ->where(function (Builder $query) use ($vipType) {
                              foreach ($vipType as $type) {
                                  $query->orWhere('vip_type', $type);
                              }
                          })
                          ->inRandomOrder()
                          ->first();

        return ResponseController::success($account);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shorturl' => 'required|string',
            'dir'      => 'required|string',
            'pwd'      => 'string',
            'page'     => 'numeric',
            'num'      => 'numeric',
            'order'    => 'string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http     = new Client([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Linux; Android 7.1.1; MI 6 Build/NMF26X; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.132 MQQBrowser/6.2 TBS/043807 Mobile Safari/537.36 MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN miniProgram',
                    'Cookie'     => config('94list.fake_cookie'),
                    'Referer'    => 'https://pan.baidu.com/disk/home'
                ]
            ]);
            $res      = $http->post('https://pan.baidu.com/share/wxlist', [
                'query'       => [
                    'channel'    => 'weixin',
                    'version'    => '2.9.6',
                    'clienttype' => 25,
                    'web'        => 1,
                    'qq-pf-to'   => 'pcqq.c2c'
                ],
                'form_params' => [
                    'shorturl' => $request['shorturl'],
                    'dir'      => $request['dir'],
                    'root'     => $request['dir'] === '/' ? 1 : 0,
                    'pwd'      => $request['pwd'] ?? '',
                    'page'     => $request['page'] ?? 1,
                    'num'      => $request['num'] ?? 1000,
                    'order'    => $request['order'] ?? 'filename'
                ]
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取文件列表');
        }

        $errno = $response['errtype'] ?? $response['errno'];
        return match ($errno) {
            0                     => ResponseController::success([
                'uk'      => $response['data']['uk'],
                'shareid' => $response['data']['shareid'],
                'randsk'  => $response['data']['seckey'],
                'list'    => $response['data']['list']
            ]),
            'mis_105'             => ResponseController::fileNotExists(),
            'mispw_9', 'mispwd-9' => ResponseController::pwdWrong(),
            'mis_2', 'mis_4'      => ResponseController::pathNotExists(),
            5                     => ResponseController::linkWrongOrPathNotExists(),
            3                     => ResponseController::linkNotValid(),
            10                    => ResponseController::linkIsOutDate(),
            8001, 9013, 9019      => ResponseController::cookieError($errno),
            default               => ResponseController::getFileListError($errno),
        };
    }

    public function getSign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uk'      => 'required|numeric',
            'shareid' => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http = new Client([
                'headers' => [
                    'User-Agent' => config("94list.fake_user_agent"),
                    'Cookie'     => config('94list.fake_cookie'),
                    'Referer'    => 'https://pan.baidu.com/disk/home'
                ]
            ]);

            $res      = $http->get('https://pan.baidu.com/share/tplconfig', [
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
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取签名信息');
        }

        $errno = $response['errtype'] ?? $response['errno'];
        return match ($errno) {
            0                     => ResponseController::success($response['data']),
            'mis_105'             => ResponseController::fileNotExists(),
            'mispw_9', 'mispwd-9' => ResponseController::pwdWrong(),
            'mis_2', 'mis_4'      => ResponseController::pathNotExists(),
            5                     => ResponseController::linkWrongOrPathNotExists(),
            3                     => ResponseController::linkNotValid(),
            10                    => ResponseController::linkIsOutDate(),
            8001, 9013, 9019      => ResponseController::cookieError($errno),
            default               => ResponseController::getSignError($errno),
        };
    }

    public function downloadFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fs_ids.*'    => 'required|numeric',
            'path_list.*' => 'required|string',
            'randsk'      => 'required|string',
            'url'         => 'required|string',
            'shareid'     => 'required|numeric',
            'uk'          => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (count($request['fs_ids']) > config('94list.max_once')) return ResponseController::linksOverloaded();

        // 获取今日解析数量
        $group   = Group::query()->find(Auth::check() ? Auth::user()['group_id'] : -1);
        $records = Record::query()
                         ->where('ip', $request->ip())
                         ->whereDate('created_at', now())
                         ->get();

        if ($records->count() >= $group['count'] || $records->sum('size') >= $group['size'] * 1073741824) return ResponseController::groupQuotaHasBeenUsedUp();

        $responseData = [];

        /**
         * account_id -1表示读取缓存的记录
         * user_id -1表示游客
         */

        // 读取缓存
        foreach ($request['fs_ids'] as $index => $fs_id) {
            $record = Record::query()
                            ->where([
                                'fs_id' => $fs_id,
                                ['account_id', '!=', -1],
                                ['normal_account_id', '!=', -1]
                            ])
                            ->whereDate('created_at', now())
                            ->whereTime('created_at', '>=', now()->subHours(8))
                            ->latest()
                            ->first();

            if (!$record) continue;

            $responseData[]    = [
                'filename' => $record['filename'],
                'url'      => $record['url'],
                'ua'       => $record['ua']
            ];
            $request['fs_ids'] = array_filter($request['fs_ids'], fn($Fs_id) => $Fs_id !== $fs_id);
            RecordController::addRecord([
                'ip'                => $request->ip(),
                'fs_id'             => $fs_id,
                'filename'          => $record['filename'],
                'user_id'           => Auth::check() ? Auth::user()['id'] : -1,
                'account_id'        => -1,
                'normal_account_id' => -1,
                'size'              => $record['size'],
                'ua'                => $record['ua'],
                'url'               => $record['url']
            ]);
        }

        if (count($request['fs_ids']) === 0) return ResponseController::success($responseData);

        $normalCookieRes  = self::getRandomCookie($request, ['普通用户', '普通会员']);
        $normalCookieData = $normalCookieRes->getData(true);
        if ($normalCookieData['data'] === null) return ResponseController::normalAccountIsNotEnough();

        // 获取签名信息
        $signRes  = self::getSign($request);
        $signData = $signRes->getData(true);
        if ($signData['code'] !== 200) return $signRes;
        $request['sign']      = $signData['data']['sign'];
        $request['timestamp'] = $signData['data']['timestamp'];

        $bodyArr = [
            'encrypt=0',
            'extra=' . urlencode('{"sekey":"' . urldecode($request['randsk']) . '"}'),
            'product=share',
            'timestamp=' . $request['timestamp'],
            'uk=' . $request['uk'],
            'primaryid=' . $request['shareid'],
            'fid_list=[' . join(',', $request['fs_ids']) . ']',
            'path_list=[' . join(',', array_map(fn($path) => '"' . $path . '"', $request['path_list'])) . ']'
        ];

        $body = join('&', $bodyArr);

        $userAgent = config('94list.user_agent');

        try {
            $http     = new Client([
                'headers' => [
                    'User-Agent' => config('94list.fake_user_agent'),
                    'Cookie'     => $normalCookieData['data']['cookie'],
                    'Host'       => 'pan.baidu.com',
                    'Origin'     => 'https://pan.baidu.com',
                    'Referer'    => $request['url']
                ]
            ]);
            $res      = $http->post('https://pan.baidu.com/api/sharedownload', [
                'query' => [
                    'channel'    => 'chunlei',
                    'clienttype' => 12,
                    'web'        => 1,
                    'app_id'     => 250528,
                    'sign'       => $request['sign'],
                    'timestamp'  => $request['timestamp'],
                ],
                'body'  => $body
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取dlink');
        }

        switch ($response['errno']) {
            case 0:
                // 如果就一个文件就不睡
                // 有多个文件就每个睡一觉
                $sleepTime = count($response['list']) > 1 ? config('94list.sleep') : 0;

                foreach ($response['list'] as $list) {
                    $svipCookieRes  = self::getRandomCookie($request);
                    $svipCookieData = $svipCookieRes->getData(true);
                    if ($svipCookieData['data'] === null) return ResponseController::svipAccountIsNotEnough();

                    Account::query()
                           ->find($svipCookieData['data']['id'])
                           ->update([
                               'last_use_at' => date('Y-m-d H:i:s')
                           ]);

                    $http = new Client([
                        'headers' => [
                            'User-Agent' => config('94list.user_agent'),
                            'Cookie'     => $svipCookieData['data']['cookie'],
                            'Host'       => 'pan.baidu.com',
                            'Origin'     => 'https://pan.baidu.com',
                            'Referer'    => $request['url']
                        ]
                    ]);

                    try {
                        $headResponse = $http->head($list['dlink'], [
                            'allow_redirects' => [
                                'follow_redirects' => false,
                                'track_redirects'  => true,
                            ]
                        ]);

                        // 获取最后一个重定向的 URL
                        $redirectUrls  = $headResponse->getHeader('X-Guzzle-Redirect-History');
                        $effective_url = end($redirectUrls);

                        if (!$effective_url) {
                            Account::query()
                                   ->find($svipCookieData['data']['id'])
                                   ->update([
                                       'switch' => 0,
                                       'reason' => '获取reallink返回空'
                                   ]);
                            return ResponseController::getRealLinkError();
                        }

                        // 账号限速
                        if (str_contains($effective_url, 'qdall01') || !str_contains($effective_url, 'tsl=0')) {
                            Account::query()
                                   ->find($svipCookieData['data']['id'])
                                   ->update([
                                       'switch' => 0,
                                       'reason' => '账户限速'
                                   ]);

                            return ResponseController::accountHasBeenLimitOfTheSpeed();
                        }

                        $responseData[] = [
                            'url'      => $effective_url,
                            'filename' => $list['server_filename'],
                            'ua'       => $userAgent
                        ];

                        RecordController::addRecord([
                            'ip'                => $request->ip(),
                            'fs_id'             => $list['fs_id'],
                            'filename'          => $list['server_filename'],
                            'user_id'           => Auth::user()['id'] ?? -1,
                            'account_id'        => $svipCookieData['data']['id'],
                            'normal_account_id' => $normalCookieData['data']['id'],
                            'size'              => $list['size'],
                            'ua'                => $userAgent,
                            'url'               => $effective_url
                        ]);
                    } catch (RequestException $e) {
                        return ResponseController::getRealLinkError();
                    } catch (GuzzleException $e) {
                        return ResponseController::networkError('获取reallink');
                    }
                    sleep($sleepTime);
                }
                return ResponseController::success($responseData);
            case -1:
                return ResponseController::linkNotValid();
            case -9:
                return ResponseController::fileNotExists();
            case 2:
                return ResponseController::downloadError();
            case 110:
                return ResponseController::ipHasBeenBaned();
            case 112:
                return ResponseController::signIsOutDate();
            case 113:
            case 118:
                return ResponseController::paramsError();
            case 116:
                return ResponseController::linkIsOutDate();
            case 121:
                return ResponseController::processFilesTooMuch();
            case 4:
                Account::query()
                       ->find($normalCookieData['data']['id'])
                       ->update([
                           'switch' => 0,
                           'reason' => 'cookie已失效'
                       ]);
                return ResponseController::accountExpired();
            case -20:
            case 9019:
                Account::query()
                       ->find($normalCookieData['data']['id'])
                       ->update([
                           'switch' => 0,
                           'reason' => '触发验证码'
                       ]);
                return ResponseController::hitCaptcha();
            case 8001:
            case 9013:
            case -6:
                Account::query()
                       ->find($normalCookieData['data']['id'])
                       ->update([
                           'switch' => 0,
                           'reason' => '获取dlink时失败'
                       ]);
                return ResponseController::getDlinkError($response['errno']);
            default:
                return ResponseController::getDlinkError($response['errno']);
        }
    }
}
