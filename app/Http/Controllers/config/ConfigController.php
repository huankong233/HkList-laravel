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
        return ResponseController::success(config('94list'));
    }

    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sleep'          => 'numeric',
            'max_once'       => 'numeric',
            'password'       => 'string',
            'announce'       => 'string',
            'user_agent'     => 'string',
            'need_inv_code'  => 'bool',
            'whitelist_mode' => 'bool'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if ($request['sleep']) $update['_94LIST_SLEEP'] = $request['sleep'];
        if ($request['max_once']) $update['_94LIST_MAX_ONCE'] = $request['max_once'];
        if ($request['password']) $update['_94LIST_PASSWORD'] = $request['password'];
        if ($request['announce']) $update['_94LIST_ANNOUNCE'] = '"' . $request['announce'] . '"';
        if ($request['user_agent']) $update['_94LIST_USER_AGENT'] = $request['user_agent'];
        if ($request['need_inv_code']) $update['_94LIST_NEED_INV_CODE'] = $request['need_inv_code'];
        if ($request['whitelist_mode']) $update['_94LIST_WHITELIST_MODE'] = $request['whitelist_mode'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
