import update
from get_version import get_version
from variables import *

# 获取版本号
local_version = get_version(local_env_path)
latest_version = get_version(latest_env_path)

# 调用update函数
try:
    # 尝试执行update函数
    update.update_code(local_version, latest_version, local_html_path, old_html_path, latest_html_path, env_name)
    # 如果没有抛出异常，就输出完成运行
    print("完成运行")
except Exception as e:
    # 如果捕获到异常，就输出出问题了，并打印异常信息
    print("无法运行")
    print(e)
