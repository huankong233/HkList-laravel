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

# 判断环境变量APP_AUTO_UPDATE的值
if [ "$APP_AUTO_UPDATE" = "true" ]; then
    # 值为true时
<<<<<<< HEAD
    php artisan app:check-app-status
=======
    python3 /python/check_update.py
    # 等待运行结束后再执行底部的exec "$@"
>>>>>>> 1524e35d18730ee217f65a17d70307ccbe9309af
elif [ "$APP_AUTO_UPDATE" = "false" ]; then
    # 值为false时
    echo "没有开启更新检测…"
else
    # 其他情况
    echo "变量错误，有效参数为\"true\"与\"false\"，当前默认不启动更新检测…"
fi && \

exec "$@"
