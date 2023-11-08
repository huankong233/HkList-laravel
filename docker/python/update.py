# 导入os系统模块
import os


def update_code(local_version, latest_version, local_html_path, old_html_path, latest_html_path, env_name):
    """更新源码和数据库"""
# 开始执行
    print("开始备份当前版本…")

# 源码更新
    # 创建版本文件夹
    if not os.path.isdir(f"{old_html_path}/{local_version}"):
        os.makedirs(f"{old_html_path}/{local_version}")
    else:
        print(f"\"{old_html_path}/{local_version}\"已存在，清空文件夹并开始重新备份…")
        os.system(f"rm -rf {old_html_path}/{local_version}")

    # 备份老版本源码
    os.system(f"cp -rp {local_html_path}/. {old_html_path}/{local_version}")
    # 清空 html 下所有内容
    os.system(f"rm -rf {local_html_path}/.* {local_html_path}/* 2> /dev/null")

    # 复制新版本源码
    print(f"开始导入v{latest_version}源码…")
    os.system(f"cp -a {latest_html_path}/. {local_html_path}/")

# 恢复本地 sqlite 数据库
    if os.path.isfile(f"{old_html_path}/{local_version}/database/database.sqlite"):
        # 恢复本地数据库
        os.system(f"cp -a -f {old_html_path}/{local_version}/database/database.sqlite {local_html_path}/database/")
        # 输出结果
        print("本地\"sqlite\"数据库恢复完成…")
    else:
        # 没有本地数据库
        print("本地\"sqlite\"数据库文件不存在，无需恢复…")

# 版本号替换
    # 恢复原.env文件
    print("导入原有配置文件…")
    os.system(f"cp -f {old_html_path}/{local_version}/{env_name} {local_html_path}/")
    # 输入新的版本号
    os.system(f"sed -i 's/_94LIST_VERSION=.*/_94LIST_VERSION={latest_version}/' {local_html_path}/{env_name}")

# 文件锁
    # 创建数据库锁与版本锁
    print("开始重建\"install\"锁与\"update\"锁…")
    os.system(f"touch {local_html_path}/install.lock")
    os.system(f"touch {local_html_path}/update.lock")
    # 写入描述
    os.system(f"echo 'install ok' > {local_html_path}/install.lock")
    os.system(f"echo 'v{latest_version}' > {local_html_path}/update.lock")

# 输出结果
    print(f'更新完成，当前版本为v{latest_version}~')
