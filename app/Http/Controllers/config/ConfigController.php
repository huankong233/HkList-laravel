<?php

namespace App\Http\Controllers\config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        return ResponseController::success(array_merge(config('94list'), ['debug' => config("app.debug")]));
    }

    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sleep'          => 'nullable|numeric',
            'max_once'       => 'nullable|numeric',
            'password'       => 'nullable|string',
            'announce'       => 'nullable|string',
            'user_agent'     => 'nullable|string',
            'need_inv_code'  => 'nullable|bool',
            'whitelist_mode' => 'nullable|bool',
            'debug'          => 'nullable|bool'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if ($request['sleep'] !== null) $update['_94LIST_SLEEP'] = $request['sleep'];
        if ($request['max_once'] !== null) $update['_94LIST_MAX_ONCE'] = $request['max_once'];
        if ($request['password'] !== null) $update['_94LIST_PASSWORD'] = $request['password'];
        if ($request['announce'] !== null) $update['_94LIST_ANNOUNCE'] = '"' . $request['announce'] . '"';
        if ($request['user_agent'] !== null) $update['_94LIST_USER_AGENT'] = $request['user_agent'];
        if ($request['need_inv_code'] !== null) $update['_94LIST_NEED_INV_CODE'] = $request['need_inv_code'];
        if ($request['whitelist_mode'] !== null) $update['_94LIST_WHITELIST_MODE'] = $request['whitelist_mode'];
        if ($request['debug'] !== null) $update['APP_DEBUG'] = $request['debug'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
