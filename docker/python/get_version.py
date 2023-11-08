# 从.env文件中获取_94LIST_VERSION的值
def get_version(env_path):
    # 打开文件
    with open(env_path, "r") as f:
        # 遍历每一行
        for line in f:
            # 如果找到_94LIST_VERSION=
            if "_94LIST_VERSION=" in line:
                # 切割字符串，获取等号后面的部分
                version = line.split("=")[1]
                # 去掉换行符
                version = version.strip()
                # 如果版本号开头为v或V，去掉它
                if version.startswith("v") or version.startswith("V"):
                    version = version.lstrip("vV")
                # 返回版本号
                return version
    # 如果没有找到，返回None
    return None
