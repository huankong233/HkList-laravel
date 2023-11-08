# 导入变量函数
# 导入compare_versions函数
from compare_versions import compare_versions
# 导入check_env函数
from get_version import get_version
from variables import *

# 从check_env中获取版本号
local_version = get_version(local_env_path)
latest_version = get_version(latest_env_path)

# 调用compare_versions函数，比较两个版本号的大小
result = compare_versions(local_version, latest_version)

# 打印结果
if result == 1:
    print(f'当前版本更高，无需更改，当前版本为v{local_version} …')
elif result == -1:
    print(f'最新版本更高，当前版本为v{local_version} ，最新版本为v{latest_version} …')
elif result == 0:
    print(f'当前版本与最新版本一致，无需更改，当前版本为v{local_version} …')
elif result == 2:
    print(f'版本号出错了，无法比对版本')
else:
    print("脚本运行失败")
