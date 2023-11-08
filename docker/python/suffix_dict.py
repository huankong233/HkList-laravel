# 后缀字典
suffix_dict = {
    "final": 6,
    "stable": 5,
    "release": 4,
    "ga": 3,
    # 无后缀时自动填充94list作为默认后缀
    "94list": 2,
    "rc": 1,
    "beta": 0,
    "alpha": -1,
    # 通配符，所有不在字典内的后缀都匹配到该等级
    "*": -2
    }
