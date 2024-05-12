#!/bin/sh

# 变量
dir_path="/var/www/html"
latest_path="/var/www/94list-laravel"

# 目录映射检测
if [ -d "$dir_path" ]; then
    cd $dir_path
    # 判断文件夹是否为空
    if [ ! "$(ls -A $dir_path)" ]; then
        # 文件夹为空 复制文件夹内容
        cp -a "$latest_path"/. "$dir_path"
    fi
else
    # 文件夹不存在
    echo "没有正确映射路径…"
fi && \

# 复制 vendor 文件夹
echo "导入新的依赖…" && \
rm -rf "$dir_path"/vendor && \
cp -a "$latest_path"/vendor "$dir_path"/vendor && \

exec "$@"
