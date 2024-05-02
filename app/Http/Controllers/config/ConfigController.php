<?php

namespace App\Http\Controllers\config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        return ResponseController::success(config('94list'));
    }

    public function updateConfig(Request $request)
    {
        $update = [];

        if ($request['sleep']) $update['_94LIST_SLEEP'] = $request['sleep'];
        if ($request['max_once']) $update['_94LIST_MAX_ONCE'] = $request['max_once'];
        if ($request['password']) $update['_94LIST_PASSWORD'] = $request['password'];
        if ($request['announce']) $update['_94LIST_ANNOUNCE'] = '"' . $request['announce'] . '"';
        if ($request['user_agent']) $update['_94LIST_USER_AGENT'] = $request['user_agent'];
        if ($request['need_inv_code']) $update['_94LIST_NEED_INV_CODE'] = $request['need_inv_code'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
