<?php

namespace App\Http\Middleware;

use App\Http\Controllers\v1\ResponseController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NeedPassword
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $passwordConfig = config('94list.password');

        // 没有启用就通过
        if ($passwordConfig === '') return $next($request);

        $password = $request['password'];
        if ($password === null || $password !== $passwordConfig) return ResponseController::parsePasswordError();

        return $next($request);
    }
}
