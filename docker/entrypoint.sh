#!/bin/sh

# 变量
www_path="/var/www/html"
latest_path="/var/www/94list-laravel"
commands_path="/app/Console/Commands"

echo "检查目录映射是否正确" && \
if [ -d "$www_path" ]; then
    cd $www_path || exit
    if [ ! "$(ls -A $www_path)" ]; then
        echo "文件夹为空,导入文件"
        cp -a $latest_path/. $www_path
    fi
else
    echo "没有正确映射路径"
    exit
fi

echo "导入Commands文件夹"
rm -rf $www_path$commands_path
mkdir -p $www_path$commands_path
cp -a $latest_path$commands_path/. $www_path$commands_path

echo "导入新的依赖"
rm -rf $www_path/vendor
cp -a $latest_path/vendor/. $www_path/vendor

echo "启动更新程序"
cd $latest_path || exit
php artisan app:check-app-status

exec "$@"
