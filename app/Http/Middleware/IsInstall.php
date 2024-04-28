<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class IsInstall
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installLock = base_path('install.lock');

        // 检查是否存在 .lock 文件
        if (!file_exists($installLock)) {
            $dbConfig = config('database');

            // 如果不存在则自动创建
            if (!file_exists($dbConfig['connections']['sqlite']['database'])) file_put_contents($dbConfig['DB_DATABASE'], '');

            // 写入key
            config([
                'APP_KEY' => 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')))
            ]);

            // 导入sql
//            $installSql = database_path('sql/sqlite.sql');
//            DB::unprepared(file_get_contents($installSql));

            // 添加用户
            User::create([
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role'     => 'admin'
            ]);

            // 写入安装锁
            file_put_contents($installLock, 'install ok');
        }

        return $next($request);
    }
}
