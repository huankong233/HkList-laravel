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

### 内置更新检测 （不可降级）

# 获取本地版本号
local_version=$(grep "_94LIST_VERSION=" /var/www/html/.env | cut -d '=' -f 2)
# 获取最新版本号
latest_version=$(grep "_94LIST_VERSION=" /var/www/94list-laravel/.env | cut -d '=' -f 2)

### 比较版本号
# local=<latest
if [[ $(echo -e "$local_version\n$latest_version" | sort -V | head -n 1) == "$local_version" ]]; then
	# local=latest
	if [ "$local_version" = "$latest_version" ]; then
		echo "当前版本与内置版本一致，无需更改…"
  
	# local<latest	
	else
		echo "当前版本\"$local_version\"低于内置版本\"$latest_version\"，开始更新…" && \
		echo "开始备份当前版本…" && \
	
		### 源码更新
		# 创建版本文件夹
		if [ ! -d /var/www/html_old/"$local_version" ]; then
			mkdir -p /var/www/html_old/"$local_version"	
		else
			echo "\"/var/www/html_old/$local_version\"已存在，清空文件夹并开始重新备份…" && \
			rm -rf /var/www/html_old/"$local_version"
		fi && \
		# 备份老版本源码
		cp -rp /var/www/html/. /var/www/html_old/"$local_version" && \
		# 清空 html 下所有内容
		rm -rf /var/www/html/. && \
		# 复制新版本源码
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
		fi && \

		### 版本号替换
		# 恢复原.env文件
		cp -f /var/www/html_old/"$local_version"/.env /var/www/html/ && \
		# 输入新的版本号
		sed -i "s/_94LIST_VERSION=.*/_94LIST_VERSION=$latest_version/" /var/www/html/.env && \
  
  		### 文件锁
		# 创建数据库锁与版本锁
  		touch /var/www/html/install.lock && \
    		touch /var/www/html/update.lock && \
    		# 写入描述
		echo "install ok" > /var/www/html/install.lock && \
  		echo "$latest_version" > /var/www/html/update.lock && \

		### 输出结果
		echo "更新完成…"
	fi

### local>latest，其他情况
elif [[ $(echo -e "$local_version\n$latest_version" | sort -V | head -n 1) == $latest_version ]]; then
	echo "当前版本不需要更新…"

### 变量无参数
else
	echo "无法比对当前版本，不启动更新…"
fi && \

exec "$@"
