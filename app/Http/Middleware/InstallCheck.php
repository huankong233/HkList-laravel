<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResponseController;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            if (!$fileExists && !$request->is('install')) {
                // 判断是否是 docker 模式
                if (config("app.installMode") === '1') {
                    try {
                        $dbConfig = config('database');
                        if ($dbConfig['default'] === 'sqlite') {
                            // 如果不存在则自动创建
                            if (file_exists($dbConfig['DB_DATABASE'])) {
                                $bakPath = $dbConfig['DB_DATABASE'] . '.bak';
                                if (file_exists($bakPath)) unlink($bakPath);
                                rename($dbConfig['DB_DATABASE'], $bakPath);
                            };

                            file_put_contents($dbConfig['DB_DATABASE'], '');
                        }

                        DB::purge();
                        // db测试
                        DB::connection()->select('select 1 limit 1');

                        // 写入key
                        AdminController::modifyEnv([
                            "APP_KEY" => 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher'))),
                        ]);

                        // 导入sql
                        $installSql = database_path('sql' . DIRECTORY_SEPARATOR . $dbConfig['default'] . '.sql');
                        DB::unprepared(file_get_contents($installSql));

                        // 添加用户
                        User::insert([
                            'username' => 'admin',
                            'password' => Hash::make('admin'),
                            'is_admin' => 1
                        ]);

                        // 写入安装锁
                        $installLock = base_path() . DIRECTORY_SEPARATOR . 'install.lock';
                        file_put_contents($installLock, 'install ok');
                        return redirect("/");
                    } catch (QueryException $exception) {
                        return ResponseController::response(400, '数据库配置错误 :' . $exception->getMessage());
                    } catch (Exception $exception) {
                        return ResponseController::response(500, '异常错误 :' . $exception->getMessage());
                    }
                } else {
                    return redirect(url('/install'));
                }
            } else {
                return $next($request);
            }
        }

        if ($need === 'notInstall') {
            if (!$fileExists) {
                return $next($request);
            } else {
                return ResponseController::response(403, config("app.name") . ' 已安装');
            }
        }

        return $next($request);
    }
}
