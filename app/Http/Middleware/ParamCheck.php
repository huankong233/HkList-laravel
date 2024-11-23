<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UtilsController;
use App\Models\Ip;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ParamCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 查询指纹是否存在
        if (!$request["print"] || !$request["rand"]) return ResponseController::paramsError();

        $print = Cache::get($request["print"]);
        $rand = $request["rand"];
        $temp = $request->method() === "GET" ? $request->query() : $request->post();
        unset($temp["rand"]);

        // 校验哈希
        if ($rand !== sha1(json_encode($temp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))) return ResponseController::paramsError();

        if (!$print) {
            Cache::put($request["print"], [UtilsController::getIp()]);
            return $next($request);
        }

        if (!in_array(UtilsController::getIp(), $print)) {
            if (count($print) > 3) {
                return ResponseController::inBlackList();
            } else {
                $print[] = UtilsController::getIp();
                Cache::put($request["print"], $print);
            }
        }

        return $next($request);
    }
}
