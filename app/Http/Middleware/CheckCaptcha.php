<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ResponseController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('94list.captcha.use') !== '') {
            $response = CaptchaController::check($request);
            $data     = $response->getData(true);
            if ($data['code'] !== 200) return $response;
        }

        return $next($request);
    }
}
