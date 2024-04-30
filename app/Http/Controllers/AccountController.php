<?php

namespace App\Http\Controllers;

use App\Models\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function getAccount(Request $request, $account_id = null)
    {
        if ($account_id !== null) {
            $account = Account::query()->find($account_id);
            if (!$account) return ResponseController::accountNotExists();
            return ResponseController::success(['account' => $account]);
        }

        $accounts = Account::query()->get();
        return ResponseController::success(['accounts' => $accounts]);
    }

    public static function _getAccountInfo($cookie)
    {
        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.fakeUserAgent"),
                'cookie'     => $cookie
            ]
        ]);

        try {
            $res      = $http->get("https://pan.baidu.com/rest/2.0/xpan/nas", [
                'query' => [
                    'method' => 'uinfo'
                ]
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取百度账户信息");
        }

        return $response ? ResponseController::success($response) : ResponseController::getAccountInfoFailed();
    }

    public static function _getSvipEndAt($cookie)
    {
        $http = new Client([
            'headers' => [
                'User-Agent' => config("94list.fakeUserAgent"),
                'Cookie'     => $cookie
            ]
        ]);

        try {
            $res      = $http->get('https://pan.baidu.com/rest/2.0/membership/user', [
                'query' => [
                    'method'     => 'query',
                    'clienttype' => 0,
                    'app_id'     => 250528,
                    'web'        => 1
                ]
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError("获取SVIP到期时间");
        }

        return $response ? ResponseController::success($response) : ResponseController::getSvipEndTimeFailed();
    }

    public function getAccountInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cookie' => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $response = [];

        $accountInfoResponse = self::_getAccountInfo($request['cookie']);
        $accountInfoData     = $accountInfoResponse->getData(true);
        if ($accountInfoData['code'] !== 200) return $accountInfoResponse;
        $response['uinfo'] = $accountInfoData;

        $svipEndAtResponse = self::_getSvipEndAt($request['cookie']);
        $svipEndAtData     = $svipEndAtResponse->getData(true);
        if ($svipEndAtData['code'] !== 200) return $svipEndAtResponse;
        $response['query'] = $svipEndAtData;

        return ResponseController::success($response);
    }

    public function _getAccountItems($cookie)
    {
        $accountInfoResponse = self::_getAccountInfo($cookie);
        $accountInfoData     = $accountInfoResponse->getData(true);
        if ($accountInfoData['code'] !== 200) return $accountInfoResponse;

        $vip_type = match ($accountInfoData['data']['vip_type']) {
            0 => '普通用户',
            1 => '普通会员',
            2 => '超级会员'
        };

        $switch      = 1;
        $svip_end_at = date("Y-m-d H:i:s", 0);

        if ($vip_type === '超级会员') {
            $svipEndAtResponse = self::_getSvipEndAt($cookie);
            $svipEndAtData     = $svipEndAtResponse->getData(true);
            if ($svipEndAtData['code'] !== 200) return $svipEndAtResponse;

            $svip_end_at = date("Y-m-d H:i:s", $svipEndAtData['data']['currenttime'] + $svipEndAtData['data']['reminder']['svip']['leftseconds']);
            if ($svip_end_at < date("Y-m-d H:i:s")) $switch = 0;
        }

        return ResponseController::success([
            'baidu_name'   => $accountInfoData['data']['baidu_name'],
            'netdisk_name' => $accountInfoData['data']['netdisk_name'],
            'cookie'       => $cookie,
            'vip_type'     => $vip_type,
            'switch'       => $switch,
            'status'       => $switch === 1 ? '未使用' : '会员过期',
            'svip_end_at'  => $svip_end_at,
            'last_use_at'  => date("Y-m-d H:i:s", 0)
        ]);
    }

    public function addAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cookie' => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $accountItems     = self::_getAccountItems($request['cookie']);
        $accountItemsData = $accountItems->getData(true);
        if ($accountItemsData['code'] !== 200) return $accountItemsData;

        Account::query()->create($accountItemsData['data']);

        return ResponseController::success();
    }

    public function updateAccount(Request $request, $account_id)
    {
        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();
        $cookie = $account['cookie'];

        $accountItems     = self::_getAccountItems($cookie);
        $accountItemsData = $accountItems->getData(true);
        if ($accountItemsData['code'] !== 200) return $accountItemsData;

        $account->update($accountItemsData['data']);

        return ResponseController::success();
    }

    public function removeAccount(Request $request, $account_id)
    {
        $account = Account::query()->find($account_id);
        if (!$account) return ResponseController::accountNotExists();

        $account->delete();

        return ResponseController::success();
    }
}
