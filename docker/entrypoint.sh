#!/bin/sh

# 目录映射的变量
dir_path="/var/www/html"

# 目录映射检测
if [ -d "$dir_path" ]; then
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



### 检查内置版本号与当前使用配置是否一致

# 获取本地版本号
local_version=$(grep "_94LIST_VERSION=" /var/www/html/.env | cut -d '=' -f 2)
# 获取最新版本号
default_version=$(grep "_94LIST_VERSION=" /var/www/94list-laravel/.env | cut -d '=' -f 2)


if [ "$local_version" = "$default_version" ]; then
### local = default
	echo "当前版本与内置版本一致，无需更改…"
		
else
	### local != default
	echo "当前版本\"$local_version\"与内置版本\"$default_version\"，不一致，开始恢复到内置版本\"$default_version\"…" && \
	echo "开始备份当前版本…" && \
	
	### 源码更新
	# 创建版本文件夹用于备份
	if [ ! -d /var/www/html_old/"$local_version" ]; then
		mkdir -p /var/www/html_old/"$local_version"	
	else
		echo "\"/var/www/html_old/$local_version\"已存在，清空文件夹并开始重新备份当前版本…" && \
		rm -rf /var/www/html_old/"$local_version"
	fi && \
	# 备份当前版本源码
	cp -rp /var/www/html/. /var/www/html_old/"$local_version" && \
	# 清空 html 下所有内容
	rm -rf /var/www/html/. && \
	# 导入新的版本源码
	cp -a /var/www/94list-laravel/. /var/www/html/ && \

	
	### 恢复本地 sqlite 数据库恢复
	if [ -f /var/www/html_old/"$local_version"/database/database.sqlite ]; then
		# 恢复本地数据库
		cp -a -f /var/www/html_old/"$local_version"/database/database.sqlite /var/www/html/database/
		# 输出结果
		echo "本地\"sqlite\"数据库恢复完成…"
	else
		# 没有本地数据库
		echo "本地\"sqlite\"数据库文件不存在，无需恢复…"
	fi

	### 版本号替换
	# 恢复原.env文件
	cp -f /var/www/html_old/"$local_version"/.env /var/www/html/ && \
	# 输入新的版本号
	sed -i "s/_94LIST_VERSION=.*/_94LIST_VERSION=$default_version/" /var/www/html/.env
	
	### 输出结果
	echo "配置完成当前版本为\"$default_version\"…"
fi

exec "$@"
