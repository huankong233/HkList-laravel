#!/bin/sh

dir_path="/var/www/html"
if [ -d "$dir_path" ]; then
    # 判断文件夹是否为空
    if [ ! "$(ls -A $dir_path)" ]; then
        # 文件夹为空 复制文件夹内容
        cp -a /var/www/94list-laravel/* /var/www/html/
        cp -a /var/www/94list-laravel/.env /var/www/html
    else
        # 文件夹不为空
        chown -R nobody /var/www/html
        chgrp -R nobody /var/www/html
        chmod -R 755 /var/www/html/
    fi
else
    # 文件夹不存在
    echo "没有正确映射路径~"
fi

exec "$@"