<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckAppStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-app-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查程序状态并在需要时更新';

    public function getVersionString($env_arr): string
    {
        return $env_arr->filter(fn($env) => Str::contains($env, '_94LIST_VERSION='))->map(fn($env) => $env ? explode('=', $env)[1] : '0.0.0')->first();
    }

    public function getEnvFile($env_path): Collection
    {
        return collect(explode("\n", File::get($env_path)));
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('开始检查是否更新');

        // 各项文件夹目录
        $www_path     = '/var/www/html';
        $bak_path     = '/var/www/html_bak';
        $bak_www_path = '/var/www/html/bak';
        $latest_path  = '/var/www/94list-laravel';

        // .env文件的路径
        $www_env_path    = $www_path . '/.env';
        $latest_env_path = $latest_path . '/.env';

        $www_env        = $this->getEnvFile($www_env_path);
        $latest_env     = $this->getEnvFile($latest_env_path);
        $www_version    = $this->getVersionString($www_env);
        $latest_version = $this->getVersionString($latest_env);

        $this->info('本地版本号：' . $www_version);
        $this->info('容器版本号：' . $latest_version);

        if ($www_version === $latest_version) {
            $this->info('本地版本和容器版本一致，无需更新');
            return;
        }

        if (version_compare($www_version, $latest_version, '>=')) {
            $this->info('本地版本高于或等于容器版本，无需更新');
            return;
        }

        $this->info('本地版本低于容器版本，开始更新');

        // 备份当前版本
        $this->info('开始备份当前版本');
        if (!File::exists($bak_path)) File::makeDirectory($bak_path);
        // 如果当前文件夹里存在备份 就移动
        if (File::exists($bak_www_path)) File::moveDirectory($bak_www_path, $bak_path, true);
        $bak_version_path = $bak_path . '/' . $www_version;
        // 如果存在就删除掉
        if (File::exists($bak_version_path)) File::deleteDirectories($bak_version_path);
        // 复制一份
        File::copyDirectory($www_path, $bak_version_path);
        $this->info('完成备份当前版本');

        $this->info('开始导入容器版本源码');
        // 清空当前版本下所有内容
        File::deleteDirectories($www_path);
        File::copyDirectory($latest_path, $www_path);
        $this->info('完成导入容器版本源码');

        // 复制数据库
        $bak_sqlite_db_file = $bak_version_path . '/database/database.sqlite';
        $www_sqlite_db_file = $www_path . '/database/database.sqlite';
        if (File::exists($bak_sqlite_db_file)) {
            File::copy($bak_sqlite_db_file, $www_sqlite_db_file);
            $this->info('本地sqlite数据库存在，导入完成');
        } else {
            $this->info('本地sqlite数据库不存在，无需导入');
        }

        // 删除配置文件
        File::delete($www_env_path);

        // 更新env信息
        $latest_env->map(fn($env, $key) => $www_env->get($key) ?? $env);
        $latest_env['_94LIST_VERSION'] = $latest_version;
        File::replace($www_path, $latest_env->implode("\n"));

        // 重建文件锁
        $this->info('重建文件锁');
        File::replace($www_path . '/install.lock', 'install ok');
        File::replace($www_path . '/update.lock', $latest_version);

        # 更新完成
        $this->info('更新完成');
    }
}
