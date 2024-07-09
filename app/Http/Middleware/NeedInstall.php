<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class NeedInstall
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->url(), "install")) return $next($request);

        // 检查是否安装
        $dbDefault = config("database.default");
        if (
            $dbDefault === "no" ||
            ($dbDefault === "sqlite" && !File::exists(database_path("database.sqlite")))
        ) {
            return response()->redirectTo("/install");
        }

        return $next($request);
    }
}
