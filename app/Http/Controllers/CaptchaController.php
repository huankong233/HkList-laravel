<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaptchaController extends Controller
{
    public static function verify(Request $request)
    {
        return match (config('captcha.use')) {
            'VAPTCHA' => self::VAPTCHA_verify($request),
            default   => ResponseController::unknownCaptcha(),
        };
    }

    public static function updateConfig($use, Request $request)
    {
        return match ($use) {
            'VAPTCHA' => self::VAPTCHA_updateConfig($request),
            default   => ResponseController::unknownCaptcha(),
        };
    }

    public function VAPTCHA_verify(Request $request)
    {
        $id        = config('captcha.VAPTCHA.vid');
        $secretkey = config('captcha.VAPTCHA.key');
        $scene     = config('captcha.VAPTCHA.scene');
        $ip        = $request->ip();

        $validator = Validator::make($request->all(), [
            'server' => ['required', 'string', 'regex:/https:\/\/.*\.vaptcha\.(com|net)/i'],
            'token'  => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $http = new Client();

        try {
            $res      = $http->post($request['server'], [
                'json' => [
                    'id'        => $id,
                    'secretkey' => $secretkey,
                    'scene'     => $scene,
                    'token'     => $request['token'],
                    'ip'        => $ip
                ]
            ]);
            $response = json_decode($res->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
        } catch (GuzzleException $e) {
            return ResponseController::networkError('校验验证码');
        }

        return $response && $response['success'] === 1 ? ResponseController::captchaSuccess() : ResponseController::captchaFailed();
    }

    public function VAPTCHA_updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vaptcha_vid'   => 'string',
            'vaptcha_key'   => 'string',
            'vaptcha_scene' => 'numeric',
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if ($request['vaptcha_vid']) $update['_94LIST_CAPTCHA_VAPTCHA_VID'] = $request['vaptcha_vid'];
        if ($request['vaptcha_key']) $update['_94LIST_CAPTCHA_VAPTCHA_KEY'] = $request['vaptcha_key'];
        if ($request['vaptcha_scene']) $update['_94LIST_CAPTCHA_VAPTCHA_SCENE'] = $request['vaptcha_scene'];

        return ResponseController::success($update);
    }
}
