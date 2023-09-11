<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $need): Response
    {
        $installLock = base_path() . DIRECTORY_SEPARATOR . 'install.lock';
        $fileExists  = file_exists($installLock);

        if ($need === 'haveInstall') {
            if (!$fileExists) {
                return redirect(url('/install'));
            } else {
                return $next($request);
            }
        }

        if ($need === 'notInstall') {
            if (!$fileExists) {
                return $next($request);
            } else {
                return ResponseController::response(400, config("app.name") . ' 已安装');
            }
        }

        return $next($request);
    }
}
