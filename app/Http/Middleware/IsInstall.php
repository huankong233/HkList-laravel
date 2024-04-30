<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use App\Models\Group;
use App\Models\User;
use Closure;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // 复制 .env 文件
            if (!file_exists(base_path(".env"))) copy(base_path('.env.example'), base_path('.env'));

            $dbFile = database_path('database.sqlite');

            // 如果不存在则自动创建
            if (file_exists($dbFile)) return ResponseController::dbFileExists();
            file_put_contents($dbFile, '');

            $key = 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')));

            // 写入key
            updateEnv("APP_KEY", $key);
            config(['APP_KEY' => $key]);

            // 导入sql
            $installSql = database_path('sql/sqlite.sql');
            DB::unprepared(file_get_contents($installSql));

            Group::query()->create([
                'id'    => 0,
                'name'  => '默认分组',
                'count' => 10,
                'size'  => 20
            ]);

            // 添加用户
            User::query()->create([
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role'     => 'admin',
                'group_id' => 0
            ]);

            // 写入安装锁
            file_put_contents($installLock, 'install ok');
        }

        return $next($request);
    }
}
