<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Record;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ParseController extends Controller
{
    public function getConfig(Request $request)
    {
        $config = config('94list');

        return ResponseController::success([
            'announce'      => $config['announce'],
            'user_agent'    => $config['user_agent'],
            'debug'         => config('app.debug'),
            'max_once'      => $config['max_once'],
            'have_account'  => self::getRandomCookie($request)->getData(true)['data'] !== null,
            'have_login'    => Auth::check(),
            'need_inv_code' => $config['need_inv_code'],
            'need_password' => $config['password'] !== '',
            'need_captcha'  => config("captcha.use") !== '',
        ]);
    }

    public static function getRandomCookie(Request $request, $vipType = '超级会员')
    {
        if ($vipType === '超级会员') {
            // 禁用不可用的账户
            $banAccounts = Account::query()
                                  ->where([
                                      'switch'   => '1',
                                      'vip_type' => '超级会员',
                                      ['svip_end_at', '<', "DATETIME('now', 'utc', '+16 hours')"]
                                  ])
                                  ->get();

            if ($banAccounts->count() !== 0) {
                if (config('mail.switch')) {
                    Mail::raw('亲爱的' . config('mail.to.name') . ':\n\t有账户已过期,详见:' . json_encode($banAccounts->toJson()), function ($message) {
                        $message->to(config('mail.to.address'))->subject('有账户过期了~');
                    });
                }

                // 更新账户状态
                foreach ($banAccounts as $account) {
                    $updateRes  = AccountController::updateAccount($request, $account['id']);
                    $updateData = $updateRes->getData(true);
                    if ($updateData['code'] !== 200) {
                        $account->update([
                            'switch' => 0,
                            'reason' => $updateData['message']
                        ]);
                    }
                    sleep(0.3);
                }
            }
        }

        $vipType = is_array($vipType) ? $vipType : [$vipType];

        $account = Account::query()
                          ->orderByRaw('RANDOM()')
                          ->firstWhere([
                              'switch' => '1'
                          ])
                          ->where(function (Builder $query) use ($vipType) {
                              foreach ($vipType as $item) {
                                  $query->orWhere("vip_type", $item);
                              }
                          })->first();

        return ResponseController::success($account);
    }

    public function getFileList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url'   => 'required|string',
            'dir'   => 'string',
            'root'  => 'boolean',
            'pwd'   => 'string',
            'num'   => 'numeric',
            'order' => 'string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        try {
            $http     = new Client([
                'headers' => [
                    'User-Agent' => 'netdisk',
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
                    'shorturl' => $request['url'],
                    'dir'      => $request['dir'] ?? null,
                    'root'     => $request['dir'] === '' || $request['dir'] === null || $request['dir'] === '/' ? 1 : 0,
                    'pwd'      => trim($request['pwd']) ?? '',
                    'page'     => $request['page'] ?? 1,
                    'num'      => $request['num'] ?? 9999,
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
                    'User-Agent' => 'netdisk',
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
            'fs_ids.*'  => 'required|numeric',
            'randsk'    => 'required|string',
            'shareid'   => 'required|numeric',
            'timestamp' => 'required|numeric',
            'uk'        => 'required|numeric',
            'sign'      => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (count($request['fs_ids']) > config('94list.max_once')) return ResponseController::linksOverloaded();

        $normalCookieRes  = self::getRandomCookie($request, ['普通用户', '普通会员']);
        $normalCookieData = $normalCookieRes->getData(true);
        if ($normalCookieData['data'] === null) return ResponseController::normalAccountIsNotEnough();

        // 检查 时间戳 是否有效
        if (time() - $request['timestamp'] > 300) {
            // 重新获取
            $signRes  = self::getSign($request);
            $signData = $signRes->getData(true);
            if ($signData['code'] !== 200) return $signRes;
            $request['sign']      = $signData['data']['sign'];
            $request['timestamp'] = $signData['data']['timestamp'];
        }

        $responseData = [];

        // 读取缓存
        foreach ($request['fs_ids'] as $index => $fs_id) {
            $record = Record::query()
                            ->where([
                                'fs_id' => $fs_id,
                                ['created_at', '<', "DATETIME('now', 'utc', '+24 hours')"],
                            ])
                            ->get()
                            ->last();

            if ($record) {
                $responseData[]    = [
                    'filename' => $record['filename'],
                    'url'      => $record['url'],
                    'ua'       => $record['ua']
                ];
                $request['fs_ids'] = array_slice($request['fs_ids'], $index + 1, count($request['fs_ids']) - 1);
            }
        }

        try {
            $http = new Client([
                'headers' => [
                    'User-Agent' => config('94list.fake_user_agent'),
                    'Cookie'     => $normalCookieData['data']['cookie'],
                    'Referer'    => 'https://pan.baidu.com/disk/home'
                ]
            ]);
            if (str_contains($request['randsk'], '%')) $request['randsk'] = urldecode($request['randsk']);
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
                    'extra=' . urlencode('{"sekey":"' . $request['randsk'] . '"}'),
                    'fid_list=[' . join(',', $request['fs_ids']) . ']',
                    'primaryid=' . $request['shareid'],
                    'uk=' . $request['uk'],
                    'product=share',
                    'type=nolimit'
                ])
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
                            'Referer'    => 'https://pan.baidu.com/disk/home',
                            'Host'       => 'pan.baidu.com',
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
                            'filename' => $list['server_filename']
                        ];

                        RecordController::addRecord([
                            'ip'         => $request->ip(),
                            'fs_id'      => $list['fs_id'],
                            'user_id'    => Auth::user()['id'] ?? -1,
                            'account_id' => $svipCookieData['data']['id'],
                            'size'       => $list['size'],
                            'ua'         => config('94list.user_agent'),
                            'url'        => $effective_url
                        ]);
                    } catch (GuzzleException $e) {
                        return ResponseController::getRealLinkError();
                    }
                    sleep($sleepTime);
                }
                return ResponseController::success($responseData);
            case -1:
                return ResponseController::linkNotValid();
            case -9:
                return ResponseController::fileNotExists();
            case -20:
                return ResponseController::hitCaptcha();
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
            case 8001:
            case 9013:
            case 9019:
            case -6:
                Account::query()
                       ->find($normalCookieData['data']['id'])
                       ->update([
                           'switch' => 0,
                           'reason' => '获取dlink时失败'
                       ]);
            default:
                return ResponseController::getDlinkError($response['errno']);
        }
    }
}
