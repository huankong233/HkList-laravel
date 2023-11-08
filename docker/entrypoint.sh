#!/bin/sh

# 目录映射的变量
dir_path="/var/www/html"

# 目录映射检测
if [ -d "$dir_path" ]; then
    cd $dir_path
    # 判断文件夹是否为空
    if [ ! "$(ls -A $dir_path)" ]; then
        # 文件夹为空 复制文件夹内容
        cp -a /var/www/94list-laravel/. "$dir_path"
    else
        # 文件夹不为空
        chown -R nobody "$dir_path" && \
        chgrp -R nobody "$dir_path" && \
        chmod -R 755 "$dir_path"/
    fi
else
    # 文件夹不存在
    echo "没有正确映射路径…"
fi && \

# 兼容老版本不存在 复制自定义命令文件夹
commands_path=$dir_path/app/Console/Commands
if [ ! -d "$commands_path" ]; then
    echo "检测到老版本,开始迁移"
    mkdir $commands_path
    # 判断文件夹是否为空
    if [ ! "$(ls -A $commands_path)" ]; then
        # 文件夹为空 复制文件夹内容
        cp -r /var/www/94list-laravel/app/Console/Commands /var/www/html/app/Console
        # 复制 vendor 文件夹
        rm -rf "$dir_path/vendor"
        cp -r /var/www/94list-laravel/vendor "$dir_path/vendor"
    else
        # 文件夹不为空
        chown -R nobody "$commands_path" && \
        chgrp -R nobody "$commands_path" && \
        chmod -R 755 "$commands_path"/
    fi
fi

# 判断环境变量APP_AUTO_UPDATE的值
if [ "$APP_AUTO_UPDATE" = "true" ]; then
    # 值为true时
    cd /var/www/94list-laravel
    php artisan app:check-app-status
elif [ "$APP_AUTO_UPDATE" = "false" ]; then
    # 值为false时
    echo "没有开启更新检测…"
else
    # 其他情况
    echo "变量错误，有效参数为\"true\"与\"false\"，当前默认不启动更新检测…"
fi && \

exec "$@"
