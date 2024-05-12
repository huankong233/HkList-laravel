<?php

use App\Http\Middleware\IpFilter;
use App\Http\Middleware\NeedInstall;
use App\Http\Middleware\NeedPassword;
use App\Http\Middleware\RoleFilter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(basePath: dirname(__DIR__))
                  ->withRouting(
                      web: __DIR__ . '/../routes/web.php',
                      api: __DIR__ . '/../routes/api.php',
                      commands: __DIR__ . '/../routes/console.php',
                      health: '/up',
                  )
                  ->withMiddleware(function (Middleware $middleware) {
                      $middleware->alias([
                          'RoleFilter'   => RoleFilter::class,
                          'IpFilter'     => IpFilter::class,
                          'NeedInstall'  => NeedInstall::class,
                          'NeedPassword' => NeedPassword::class,
                      ]);

                      $middleware->web(remove: [
                          StartSession::class,
                      ]);

                      $middleware->use([
                          StartSession::class
                      ]);

                      $middleware->group('ThrottleRequest', [
                          // 限制1分钟内请求不超过50次 超过就停用10分钟
                          ThrottleRequests::class . ':30,10'
                      ]);
                  })
                  ->withExceptions(function (Exceptions $exceptions) {
                      //
                  })->create();
