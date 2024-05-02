<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CaptchaController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NeedCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('captcha.use') !== '') {
            $captchaRes  = CaptchaController::verify($request);
            $captchaData = $captchaRes->getData(true);
            if ($captchaData['code'] !== 200) return $captchaRes;
        }

        return $next($request);
    }
}
