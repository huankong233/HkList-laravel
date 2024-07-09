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
    protected $signature = "app:check-app-status";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "检查程序状态并在需要时更新";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("开始检查是否更新");

        // 各项文件夹目录
        $www_path    = "/var/www/html";
        $latest_path = "/var/www/94list-laravel";

        // .env文件的路径
        $www_env_path    = $www_path . "/.env";
        $latest_env_path = $latest_path . "/.env";

        $www_env    = getEnvFile($www_env_path);
        $latest_env = getEnvFile($latest_env_path);

        $www_version    = getVersionString($www_env);
        $latest_version = getVersionString($latest_env);

        $this->info("本地版本号：" . $www_version);
        $this->info("容器版本号：" . $latest_version);

        if ($www_version === $latest_version) {
            $this->info("本地版本和容器版本一致，无需更新");
            return;
        }

        if (version_compare($www_version, $latest_version, ">=")) {
            $this->info("本地版本高于或等于容器版本，无需更新");
            return;
        }

        $this->info("本地版本低于容器版本，开始更新");

        $bak_path = "/var/www/bak";

        $www_db_file = $www_path . "/database/database.sqlite";
        $bak_db_file = $bak_path . "/database.sqlite";

        $www_env_file = $www_path . "/.env";
        $bak_env_file = $bak_path . "/.env";

        $this->info("开始备份数据库和配置文件");
        if (!File::exists($bak_path)) File::makeDirectory($bak_path);
        if (File::exists($www_db_file)) File::copy($www_db_file, $bak_db_file);
        if (File::exists($www_env_file)) File::copy($www_env_file, $bak_env_file);
        $this->info("完成备份数据库和配置文件");

        $this->info("开始导入容器版本源码");
        // 清空当前版本下所有内容
        File::deleteDirectories($www_path);
        File::copyDirectory($latest_path, $www_path);
        $this->info("完成导入容器版本源码");

        $this->info("开始导入sqlite数据库");
        if (File::exists($bak_db_file)){
          File::copy($bak_db_file, $www_db_file);
          $this->info("完成导入sqlite数据库");
        }else{
          $this->info("取消导入sqlite数据库: 数据库不存在");
        }

        $this->info("开始导入配置文件");
        // 删除配置文件
        File::delete($www_env_path);
        // 更新env信息
        $latest_env = $latest_env->map(function ($env, $key) use ($www_env, $latest_version) {
            if ($key === "_94LIST_VERSION") return "_94LIST_VERSION=" . $latest_version;
            return $key . "=" . ($www_env->get($key) ?? $env);
        });
        File::replace($www_env_path, $latest_env->implode("\n"));
        $this->info("完成导入配置文件");

        # 更新完成
        $this->info("更新完成");
    }
}
