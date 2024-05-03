<?php

namespace App\Http\Controllers\config;

use App\Http\Controllers\CaptchaController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;

class CaptchaConfigController extends Controller
{
    public function getCaptchaConfig()
    {
        return ResponseController::success(config('captcha'));
    }

    public function sendCaptchaVerify(Request $request)
    {
        return CaptchaController::verify($request);
    }

    public function updateCaptchaConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'use' => 'string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if ($request['use']) $update['_94LIST_CAPTCHA_USE'] = $request['use'];

        $updateItemsRes  = CaptchaController::updateConfig($request['use'] ?? config('captcha.use'), $request);
        $updateItemsData = $updateItemsRes->getData(true);
        if ($updateItemsData['code'] !== 200) return $updateItemsRes;
        $update = array_merge($update, $updateItemsData['data']);

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
