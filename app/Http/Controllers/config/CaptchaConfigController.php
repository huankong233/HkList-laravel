<?php

namespace App\Http\Controllers\config;

use App\Http\Controllers\CaptchaController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;

class CaptchaConfigController extends Controller
{
    public function getCaptchaConfig()
    {
        return ResponseController::success(['captchaConfig' => config("captcha")]);
    }

    public function sendCaptchaVerify(Request $request)
    {
        return CaptchaController::verify($request);
    }

    public function updateCaptchaConfig(Request $request)
    {
        $update = [];

        if ($request['use']) $update['_94LIST_CAPTCHA_USE'] = $request['use'];

        $updateItems     = CaptchaController::updateConfig($request['use'] ?: config('captcha.use'), $request);
        $updateItemsData = $updateItems->getData(true);
        if ($updateItemsData['code'] !== 200) return $updateItems;
        $update = array_merge($update, $updateItemsData['data']);

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
