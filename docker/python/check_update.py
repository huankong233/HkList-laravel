# 导入check_env函数
# 导入update函数
import update
# 导入compare_versions函数
from compare_versions import compare_versions
from get_version import get_version
# 导入变量函数
from variables import *

# 调用check_env.py，获取版本号
local_version = get_version(local_env_path)
latest_version = get_version(latest_env_path)

# 调用compare_versions函数，比较两个版本号的大小
result = compare_versions(local_version, latest_version)

# 打印结果
if result == 1:
    print(f'当前版本高于最新版本，无需更改，当前版本为v{local_version} …')
elif result == -1:
    print(f'当前版本低于最新版本，当前版本为v{local_version} ，开始更新为最新版本v{latest_version} …')
    # 调用update函数
    update.update_code(local_version, latest_version, local_html_path, old_html_path, latest_html_path, env_name)
elif result == 0:
    print(f'当前版本与内置版本一致，无需更改，当前版本为v{local_version} …')
elif result == 2:
    print(f'版本号出错了，无法比对版本')
else:
    print("脚本运行失败")
