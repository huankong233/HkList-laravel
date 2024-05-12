<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Group;
use App\Models\Record;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Json;
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
            self::getRandomCookie()->getData(true)['data'] === null ||
            self::getRandomCookie(['普通用户', '普通会员'])->getData(true)['data'] === null
        )
            $have_account = false;

        return ResponseController::success([
            'announce'      => $config['announce'] === '' ? null : $config['announce'],
            'user_agent'    => $config['user_agent'],
            'debug'         => config('app.debug'),
            'max_once'      => $config['max_once'],
            'have_account'  => $have_account,
            'have_login'    => Auth::check(),
            'need_inv_code' => $config['need_inv_code'],
            'need_password' => $config['password'] !== ''
        ]);
    }

    public function getRandomCookie($vipType = '超级会员')
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
                    $updateRes  = AccountController::updateAccount($account['id']);
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
                    try {
                        Mail::raw('亲爱的' . config('mail.to.name') . ':\n\t有账户已过期,详见:' . json_encode($updateFailedAccounts), function ($message) {
                            $message->to(config('mail.to.address'))->subject('有账户过期了~');
                        });
                    } catch (Exception $e) {
                        return ResponseController::sendMailFailed($e->getMessage());
                    }
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

    public function decodeSceKey($seckey)
    {
        $seckey = str_replace("-", "+", $seckey);
        $seckey = str_replace("~", "=", $seckey);
        return str_replace("_", "/", $seckey);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surl'  => 'required|string',
            'dir'   => 'required|string',
            'pwd'   => 'string',
            'page'  => 'numeric',
            'num'   => 'numeric',
            'order' => 'string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http     = new Client([
                'headers' => [
                    'User-Agent' => config('94list.fake_wx_user_agent'),
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
                    'shorturl' => $request['surl'],
                    'dir'      => $request['dir'],
                    'root'     => $request['dir'] === '/' ? 1 : 0,
                    'pwd'      => $request['pwd'] ?? '',
                    'page'     => $request['page'] ?? 1,
                    'num'      => $request['num'] ?? 1000,
                    'order'    => $request['order'] ?? 'filename'
                ]
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取文件列表');
        }

        $errno = $response['errtype'] ?? ($response['errno'] ?? '未知');
        return match ($errno) {
            0                     => ResponseController::success([
                'uk'      => $response['data']['uk'],
                'shareid' => $response['data']['shareid'],
                'randsk'  => self::decodeSceKey($response['data']['seckey']),
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
            'surl'    => 'required|string',
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
                    'surl'       => $request['surl'],
                    'shareid'    => $request['shareid'],
                    'uk'         => $request['uk'],
                    'fields'     => 'sign,timestamp',
                    'channel'    => 'chunlei',
                    'web'        => 1,
                    'app_id'     => 250528,
                    'clienttype' => 0
                ]
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取签名信息');
        }

        $errno = $response['errtype'] ?? ($response['errno'] ?? '未知');
        return match ($errno) {
            0       => ResponseController::success($response['data']),
            default => ResponseController::getSignError($errno, $response['show_msg'] ?? '未知'),
        };
    }

    public function getDownloadLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fs_ids.*'  => 'required|numeric',
            'randsk'    => 'required|string',
            'shareid'   => 'required|numeric',
            'uk'        => 'required|numeric',
            'sign'      => 'required|string',
            'timestamp' => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (count($request['fs_ids']) > config('94list.max_once')) return ResponseController::linksOverloaded();

        // 检查限制还能不能解析
        $checkLimitRes  = self::checkLimit($request);
        $checkLimitData = $checkLimitRes->getData(true);
        if ($checkLimitData['code'] !== 200) return $checkLimitRes;

        $count = $checkLimitData['data']['count'];
        $size  = $checkLimitData['data']['size'];

        // 检查签名是否过期
        if (time() - $request['timestamp'] > 300) return ResponseController::signIsOutDate();

        // 检查普通账户是否够用
        $normalCookieRes  = self::getRandomCookie(['普通用户', '普通会员']);
        $normalCookieData = $normalCookieRes->getData(true);
        if ($normalCookieData['data'] === null) return ResponseController::normalAccountIsNotEnough();
        $normalAccountId = $normalCookieData['data']['id'];

        // 检查文件数量是否符合用户组配额
        if (count($request['fs_ids']) >= $count) return ResponseController::groupQuotaIsNotEnough();

        // 获取缓存
        $getCacheRes  = self::getCache($request);
        $getCacheData = $getCacheRes->getData(true);

        $responseData      = $getCacheData['data']['responseData'];
        $request['fs_ids'] = $getCacheData['data']['fs_ids'];

        if (count($request['fs_ids']) === 0) return ResponseController::success($responseData);

        // 获取DLink
        $getDLinkRes  = self::getDLink($request, $normalAccountId);
        $getDLinkData = $getDLinkRes->getData(true);
        if ($getDLinkData['code'] !== 200) return $getDLinkRes;
        $DlinkList = $getDLinkData['data'];

        // 检查文件大小是否符合用户组配额
        if (collect($DlinkList)->sum('size') >= $size) return ResponseController::groupQuotaIsNotEnough();

        // 获取RealLink
        $getRealLinkRes  = self::getRealLink($request, $DlinkList, $normalAccountId);
        $getRealLinkData = $getRealLinkRes->getData(true);

        // 插入缓存读取到的那部分
        $user_id = Auth::check() ? Auth::user()['id'] : -1;
        foreach ($responseData as $responseDatum) {
            RecordController::addRecord([
                'ip'                => $request->ip(),
                'fs_id'             => $responseDatum['fs_id'],
                'filename'          => $responseDatum['filename'],
                'user_id'           => $user_id,
                'account_id'        => -1,
                'normal_account_id' => -1,
                'size'              => $responseDatum['size'],
                'ua'                => $responseDatum['ua'],
                'url'               => $responseDatum['url']
            ]);
        }

        $responseData = [
            ...$responseData,
            ...$getRealLinkData['data']
        ];

        return ResponseController::success($responseData);
    }

    public function checkLimit(Request $request)
    {
        // 获取今日解析数量
        $group = Group::query()
                      ->find(Auth::check() ? Auth::user()['group_id'] : -1);

        $records = Record::query()
                         ->where('ip', $request->ip())
                         ->whereDate('created_at', now())
                         ->get();

        if ($records->count() >= $group['count'] || $records->sum('size') >= $group['size'] * 1073741824) return ResponseController::groupQuotaHasBeenUsedUp();

        return ResponseController::success([
            'group_name' => $group['name'],
            'count'      => $group['count'] - $records->count(),
            'size'       => $group['size'] * 1073741824 - $records->sum('size')
        ]);
    }

    public function getCache(Request $request)
    {
        $responseData = [];

        /**
         * account_id -1表示读取缓存的记录
         * user_id -1表示游客
         */

        // 读取缓存
        foreach ($request['fs_ids'] as $fs_id) {
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

            $responseData[] = [
                'filename' => $record['filename'],
                'url'      => $record['url'],
                'ua'       => $record['ua'],
                'size'     => $record['size'],
                'fs_id'    => $record['fs_id']
            ];

            $request['fs_ids'] = array_filter($request['fs_ids'], fn($Fs_id) => $Fs_id !== $fs_id);
        }

        return ResponseController::success([
            'responseData' => $responseData,
            'fs_ids'       => $request['fs_ids']
        ]);
    }

    public function getDLink(Request $request, $normalAccountId)
    {
        $normalAccount = Account::query()->find($normalAccountId);

        $http = new Client([
            'headers' => [
                'User-Agent' => config('94list.fake_user_agent'),
                'Cookie'     => $normalAccount['cookie'],
                'Host'       => 'pan.baidu.com',
                'Origin'     => 'https://pan.baidu.com',
                'Referer'    => 'https://pan.baidu.com/disk/home'
            ]
        ]);

        try {
            $res      = $http->post('https://pan.baidu.com/api/sharedownload', [
                'query' => [
                    'app_id'     => 250528,
                    'channel'    => 'chunlei',
                    'clienttype' => 12,
                    'sign'       => $request['sign'],
                    'timestamp'  => $request['timestamp'],
                    'web'        => 1
                ],
                'body'  => join('&', [
                    'encrypt=0',
                    'extra=' . urlencode(
                        Json::encode([
                            'sekey' => str_contains($request['randsk'], "%") ? urldecode($request['randsk']) : $request['randsk']
                        ])
                    ),
                    'fid_list=' . JSON::encode($request['fs_ids']),
                    'primaryid=' . $request['shareid'],
                    'uk=' . $request['uk'],
                    'product=share',
                    'type=nolimit'
                ])
            ]);
            $response = JSON::decode($res->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? JSON::decode($e->getResponse()->getBody()->getContents()) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('获取DLink');
        }

        $errno = $response['errtype'] ?? ($response['errno'] ?? '未知');
        switch ($errno) {
            case 0:
                $normalAccount->update([
                    'last_use_at' => date("Y-m-d H:i:s")
                ]);
                return ResponseController::success($response['list']);
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
            case -6:
                $normalAccount->update([
                    'switch' => 0,
                    'reason' => 'cookie已失效'
                ]);
                return ResponseController::accountExpired();
            case -20:
            case 9019:
                $normalAccount->update([
                    'reason' => '触发验证码'
                ]);
                return ResponseController::hitCaptcha();
            case 8001:
            case 9013:
                $normalAccount->update([
                    'switch' => 0,
                    'reason' => '获取DLink失败'
                ]);
                return ResponseController::getDlinkError($errno);
            default:
                return ResponseController::getDlinkError($errno);
        }
    }

    public function getRealLink(Request $request, $DlinkList, $normalAccountId)
    {
        // 如果就一个文件就不睡
        // 有多个文件就每个睡一觉
        $sleepTime    = count($DlinkList) > 1 ? config('94list.sleep') : 0;
        $userAgent    = config("94list.user_agent");
        $responseData = [];

        foreach ($DlinkList as $list) {
            $svipCookieRes  = self::getRandomCookie();
            $svipCookieData = $svipCookieRes->getData(true);
            if ($svipCookieData['data'] === null) {
                $responseData[] = [
                    'url'      => ResponseController::svipAccountIsNotEnough(),
                    'filename' => $list['server_filename'],
                    'ua'       => $userAgent,
                ];
                continue;
            }

            $svipAccount = Account::query()->find($svipCookieData['data']['id']);
            $svipAccount->update([
                'last_use_at' => date('Y-m-d H:i:s')
            ]);

            $http = new Client([
                'headers' => [
                    'User-Agent' => $userAgent,
                    'Cookie'     => $svipCookieData['data']['cookie'],
                    'Host'       => 'pan.baidu.com',
                    'Origin'     => 'https://pan.baidu.com',
                    'Referer'    => 'https://pan.baidu.com/disk/home'
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

                if (!$effective_url || strlen($effective_url) < 20) {
                    $svipAccount->update([
                        'switch' => 0,
                        'reason' => '获取reallink返回空'
                    ]);
                    $responseData[] = [
                        'url'      => ResponseController::getRealLinkError(),
                        'filename' => $list['server_filename'],
                        'ua'       => $userAgent,
                    ];
                    continue;
                }

                // 账号限速
                if (str_contains($effective_url, 'qdall01') || !str_contains($effective_url, 'tsl=0')) {
                    $svipAccount->update([
                        'switch' => 0,
                        'reason' => '账户限速'
                    ]);
                    $responseData[] = [
                        'url'      => ResponseController::accountHasBeenLimitOfTheSpeed(),
                        'filename' => $list['server_filename'],
                        'ua'       => $userAgent,
                    ];
                    continue;
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
                    'account_id'        => $svipAccount['id'],
                    'normal_account_id' => $normalAccountId,
                    'size'              => $list['size'],
                    'ua'                => $userAgent,
                    'url'               => $effective_url
                ]);
            } catch (RequestException $e) {
                $responseData[] = [
                    'url'      => ResponseController::getRealLinkError(),
                    'filename' => $list['server_filename'],
                    'ua'       => $userAgent,
                ];
            } catch (GuzzleException $e) {
                $responseData[] = [
                    'url'      => ResponseController::networkError('获取reallink')->getData(true)['message'],
                    'filename' => $list['server_filename'],
                    'ua'       => $userAgent,
                ];
            }
            sleep($sleepTime);
        }

        return ResponseController::success($responseData);
    }
}
