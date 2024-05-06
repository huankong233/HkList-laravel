<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IpController extends Controller
{
    public function getIp(Request $request, $ip_id = null)
    {
        if ($ip_id !== null) {
            $ip = Ip::query()->find($ip_id);
            if (!$ip) return ResponseController::IpNotExists();
            return ResponseController::success($ip);
        }

        $ips = Ip::query()->paginate($request['size']);
        return ResponseController::success($ips);
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

    public function updateIp(Request $request, $ip_id)
    {
        $validator = Validator::make($request->all(), [
            'ip'   => 'nullable|string',
            'mode' => ['nullable', Rule::in([0, 1])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $ip = Ip::query()->find($ip_id);
        if (!$ip) return ResponseController::IpNotExists();

        $update = [];

        if ($request['ip'] !== null) {
            $Ip = Ip::query()->firstWhere('ip', $request['ip']);
            if ($ip['id'] !== $Ip['id']) return ResponseController::IpExists();
            $update['ip'] = $request['ip'];
        }

        if ($request['mode'] !== null) $update['mode'] = $request['mode'];

        if (count($update) === 0) return ResponseController::paramsError();

        $ip->update($update);

        return ResponseController::success();
    }

    public function removeIp(Request $request, $ip_id)
    {
        $ip = Ip::query()->find($ip_id);
        if (!$ip) return ResponseController::IpNotExists();

        $ip->delete();

        return ResponseController::success();
    }
}
