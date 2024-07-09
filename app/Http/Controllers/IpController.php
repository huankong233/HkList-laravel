<?php

namespace App\Http\Controllers;

use App\Models\Ip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IpController extends Controller
{
    public function getIps(Request $request)
    {
        $ips = Ip::query()->paginate($request["size"]);
        return ResponseController::success($ips);
    }

    public function addIp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ip"   => "required|string|ip",
            "mode" => ["required", Rule::in([0, 1])],
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $ip = Ip::query()->firstWhere("ip", $request["ip"]);
        if ($ip) return ResponseController::IpExists();

        Ip::query()->create([
            "ip"     => $request["ip"],
            "mode"   => $request["mode"]
        ]);

        return ResponseController::success();
    }

    public function updateIp(Request $request, $ip_id)
    {
        $validator = Validator::make($request->all(), [
            "ip"     => "required|string|ip",
            "mode"   => ["required", Rule::in([0, 1])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $ip = Ip::query()->find($ip_id);
        if (!$ip) return ResponseController::IpNotExists();

        $Ip = Ip::query()->firstWhere("ip", $request["ip"]);
        if ($Ip && $ip["id"] !== $Ip["id"]) return ResponseController::IpExists();

        $ip->update([
            "ip"     => $request["ip"],
            "mode"   => $request["mode"]
        ]);

        return ResponseController::success();
    }

    public function removeIps(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ip_ids"   => "required|array",
            "ip_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Ip::query()->whereIn("id", $request["ip_ids"])->delete();

        return ResponseController::success();
    }
}
