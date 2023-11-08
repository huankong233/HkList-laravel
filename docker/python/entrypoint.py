# 导入 os 模块，用于操作系统相关的功能
import os

# 定义文件夹路径
dir_path = "/var/www/html"
# 判断文件夹是否存在
if os.path.isdir(dir_path):
    # 判断文件夹是否为空
    if not os.listdir(dir_path):
        # 文件夹为空 复制文件夹内容
        os.system("cp -a /var/www/94list-laravel/. /var/www/html/")
    else:
        # 文件夹不为空
        os.system("chown -R nobody /var/www/html")
        os.system("chgrp -R nobody /var/www/html")
        os.system("chmod -R 755 /var/www/html/")
else:
    # 文件夹不存在
    print("没有正确映射路径~")

# 判断环境变量APP_AUTO_UPDATE的值
if os.environ.get("APP_AUTO_UPDATE") == "true":
    # 值为true时
    os.system("python3 /python/check_update.py")
elif os.environ.get("APP_AUTO_UPDATE") == "false":
    # 值为false时
    print("没有开启更新检测…")
else:
    # 其他情况
    print("变量错误，有效参数为\"true\"与\"false\"，当前默认不启动更新检测…")

# 执行传入的参数
os.system(" ".join(sys.argv[1:]))
