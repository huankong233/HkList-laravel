<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IpController extends Controller
{
    public function getIp(Request $request, $ip = null)
    {
        if ($ip !== null) {
            $Ip = Ip::query()->find($ip);
            if (!$Ip) return ResponseController::IpNotExists();
            return ResponseController::success($Ip);
        }

        $Ips = Ip::query()->get();
        return ResponseController::success($Ips);
    }

    public function addIp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip'   => 'required|string',
            'mode' => ['required', Rule::in([0, 1])],
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $ip = Ip::query()->firstWhere('ip', $request['ip']);
        if ($ip) return ResponseController::IpExists();

        Ip::query()->create([
            'ip'   => $request['ip'],
            'mode' => $request['mode']
        ]);

        return ResponseController::success();
    }

    public function updateIp(Request $request, $ip)
    {
        $validator = Validator::make($request->all(), [
            'ip'   => 'string',
            'mode' => Rule::in([0, 1])
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $Ip = Ip::query()->firstWhere('ip', $ip);
        if (!$Ip) return ResponseController::IpNotExists();

        $update = [];

        if ($request['ip']) {
            if (Ip::query()->firstWhere('ip', $request['ip'])) return ResponseController::IpExists();
            $update['ip'] = $request['ip'];
        }

        if ($request['mode']) $update['mode'] = $request['mode'];

        if (count($update) === 0) return ResponseController::paramsError();

        $Ip->update($update);

        return ResponseController::success();
    }

    public function removeIp(Request $request, $ip)
    {
        $Ip = Ip::query()->firstWhere('ip', $ip);
        if (!$Ip) return ResponseController::IpNotExists();

        $Ip->delete();

        return ResponseController::success();
    }
}
