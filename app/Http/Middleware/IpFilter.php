<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UtilsController;
use App\Models\Ip;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpFilter
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = UtilsController::getIp();
        if (config("94list.whitelist_mode")) {
            // 白名单
            $ip = Ip::query()->firstWhere(['ip' => $ip, 'mode' => 1]);
            if (!$ip) return ResponseController::notInWhiteList();
        } else {
            $ip = Ip::query()->firstWhere(['ip' => $ip, 'mode' => 0]);
            if ($ip) return ResponseController::inBlackList();
        }

        return $next($request);
    }
}
