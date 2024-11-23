<?php

use App\Http\Middleware\AutoUpdate;
use App\Http\Middleware\IpFilter;
use App\Http\Middleware\NeedInstall;
use App\Http\Middleware\NeedPassword;
use App\Http\Middleware\ParamCheck;
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
                          'AutoUpdate'   => AutoUpdate::class,
                          'ParamCheck'   => ParamCheck::class
                      ]);

                      $middleware->web(remove: [
                          StartSession::class,
                      ]);

                      $middleware->use([
                          StartSession::class
                      ]);

                      $middleware->trustProxies("*");
                  })
                  ->withExceptions(function (Exceptions $exceptions) {
                      //
                  })->create();
