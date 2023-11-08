<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdminController;
use Illuminate\Console\Command;

class checkAppStatus extends Command
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
    protected $description = 'Command description';

    public function getVersionString($filePath): string
    {
        $contentArray = collect(file($filePath, FILE_IGNORE_NEW_LINES));
        foreach ($contentArray as $value) {
            if (str_starts_with($value, '_94LIST_VERSION')) {
                return explode("=", $value)[1];
            }
        }
        return '0.0.0';
    }

    /**
     * 删除目录
     * @param string $path
     * @return bool
     */
    public function dir_del(string $path, bool $delPath = false): bool
    {
        if (!is_dir($path)) {
            return false;
        }
        $open = opendir($path);
        if (!$open) {
            return false;
        }
        while (($v = readdir($open)) !== false) {
            if ('.' == $v || '..' == $v) {
                continue;
            }
            $item = $path . '/' . $v;
            if (is_file($item)) {
                unlink($item);
                continue;
            }
            $this->dir_del($item);
        }
        closedir($open);
        return !$delPath || rmdir($path);
    }

    /**
     * 文件夹文件拷贝
     *
     * @param string $src 来源文件夹
     * @param string $dst 目的地文件夹
     * @return bool
     */
    public function dir_copy(string $src = '', string $dst = ''): bool
    {
        if (empty($src) || empty($dst)) {
            return false;
        }

        $dir = opendir($src);
        $this->dir_mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->dir_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);

        return true;
    }

    /**
     * 创建文件夹
     *
     * @param string $path 文件夹路径
     * @param int $mode 访问权限
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public function dir_mkdir(string $path = '', int $mode = 0777, bool $recursive = true): bool
    {
        clearstatcache();
        if (!is_dir($path)) {
            mkdir($path, $mode, $recursive);
            return chmod($path, $mode);
        }
        return true;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('开始检查容器是否更新');

        # 各项文件夹目录与配置文件的名称
        $local_html_path  = "/var/www/html";
        $old_html_path    = "/var/www/html_old";
        $latest_html_path = "/var/www/94list-laravel";
        $env_name         = ".env";
        # 生成.env文件的路径
        $local_env_path  = $local_html_path . "/" . $env_name;
        $latest_env_path = $latest_html_path . "/" . $env_name;

        $local_version  = $this->getVersionString($local_env_path);
        $latest_version = $this->getVersionString($latest_env_path);

        $this->info("本地版本号: " . $local_version);
        $this->info("容器版本号：" . $latest_version);

        if ($local_version === $latest_version) {
            $this->info("本地版本和容器版本一致，无需更新");
            return;
        }

        if (version_compare($local_version, $latest_version, ">=")) {
            $this->info("本地版本高于或等于容器版本，无需更新");
            return;
        }

        $this->info("本地版本低于容器版本，开始更新");

        # 创建版本文件夹
        $bakPath = $old_html_path . '/' . $local_version;
        if (!file_exists($bakPath)) {
            $this->dir_mkdir($bakPath);
        } else {
            $this->warn($bakPath . "已存在，清空文件夹并开始重新备份");
            $this->dir_del($bakPath, true);
        }

        # 备份老版本源码
        $this->dir_copy($local_html_path, $bakPath);
        # 清空 html 下所有内容
        $this->dir_del($local_html_path);
        $this->dir_mkdir($local_html_path);

        # 复制新版本源码
        $this->info("开始导入容器版本源码");
        $this->dir_copy($latest_html_path, $local_html_path);

        # 复制数据库
        $sqliteDbFile = $bakPath . "/database/database.sqlite";
        if (file_exists($sqliteDbFile)) {
            copy($sqliteDbFile, $local_html_path . "/database/database.sqlite");
            $this->info("本地sqlite数据库已存在，开始导入");
        } else {
            $this->info("本地sqlite数据库不存在，无需导入");
        }

        # 更新版本号
        $bakEnvPath = $bakPath . '/' . $env_name;
        unlink($local_env_path);
        copy($bakEnvPath, $local_env_path);
        AdminController::modifyEnv([
            '_94LIST_VERSION' => $latest_version
        ], $local_env_path);

        # 重建文件锁
        $this->info("重建文件锁");
        file_put_contents($local_html_path . "/install.lock", 'install ok');
        file_put_contents($local_html_path . "/update.lock", $latest_version);

        # 更新完成
        $this->info("更新完成");
    }
}
