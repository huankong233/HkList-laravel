<?php

namespace App\Http\Controllers\config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        return ResponseController::success(['config' => config('94list')]);
    }

    public function updateConfig(Request $request)
    {
        $update = [];

        if ($request['sleep']) $update['_94LIST_SLEEP'] = $request['sleep'];
        if ($request['maxOnce']) $update['_94LIST_MAX_ONCE'] = $request['maxOnce'];
        if ($request['password']) $update['_94LIST_PASSWORD'] = $request['password'];
        if ($request['announce']) $update['_94LIST_ANNOUNCE'] = '"' . $request['announce'] . '"';
        if ($request['userAgent']) $update['_94LIST_USER_AGENT'] = $request['userAgent'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
