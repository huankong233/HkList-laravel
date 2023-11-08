from get_version import get_version
from variables import local_env_path, latest_env_path, env_name

# 检查path
print(f"当前版本\"{env_name}\"路径：{local_env_path}")
print(f"最新版本\"{env_name}\"路径：{latest_env_path}")


# 调用check_env.py，获取版本号
local_version = get_version(local_env_path)
latest_version = get_version(latest_env_path)

# 打印local和latest的值
print(f"当前版本v{local_version}")
print(f"最新版本v{latest_version}")
