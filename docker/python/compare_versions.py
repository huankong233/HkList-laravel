# 导入LooseVersion模块
from distutils.version import LooseVersion

# 导入后缀字典
from suffix_dict import suffix_dict
# 导入re模块
import re


def compare_versions(local_version, latest_version):

    # 拆分版本号
    # 分割 local_version变量
    if "-" in local_version:
        # 用“-”作为分隔符，将变量分割成一个列表
        local_version_list = local_version.split("-")

        # 判断列表的长度，如果是2，就认为有一个“-”，将前后两部分赋值给新变量
        if len(local_version_list) == 2:
            local_version_num, local_version_mark = local_version_list
            # 将后缀的次级版本号设为空字符串
            local_version_mark_num = ""

        # 判断列表的长度，如果是3或更多，就认为有两个或更多“-”，将第一部分赋值给版本号，第二部分赋值给后缀，剩余部分合并为次级版本号
        elif len(local_version_list) >= 3:
            local_version_num = local_version_list[0]
            local_version_mark = local_version_list[1]
            local_version_mark_num = "-".join(local_version_list[2:])
        # 其他情况，就返回2
        else:
            return 2
    else:
        # 如果没有“-”，就认为整个变量是版本号，后缀为local
        local_version_num = local_version
        local_version_mark = "94list"
        local_version_mark_num = ""

    # 分割 latest_version变量
    if "-" in latest_version:
        # 用“-”作为分隔符，将变量分割成一个列表
        latest_version_list = latest_version.split("-")

        # 判断列表的长度，如果是2，就认为有一个“-”，将前后两部分赋值给新变量
        if len(latest_version_list) == 2:
            latest_version_num, latest_version_mark = latest_version_list
            # 将后缀的次级版本号设为空字符串
            latest_version_mark_num = ""

        # 判断列表的长度，如果是3或更多，就认为有两个或更多“-”，将第一部分赋值给版本号，第二部分赋值给后缀，剩余部分合并为次级版本号
        elif len(latest_version_list) >= 3:
            latest_version_num = latest_version_list[0]
            latest_version_mark = latest_version_list[1]
            latest_version_mark_num = "-".join(latest_version_list[2:])
        # 其他情况，就返回2
        else:
            return 2
    else:
        # 如果没有“-”，就认为整个变量是版本号，后缀为local
        latest_version_num = latest_version
        latest_version_mark = "94list"
        latest_version_mark_num = ""

    # 定义一个正则表达式，匹配一个或多个数字，后面可以跟一个或多个.和数字的组合
    pattern = r"^\d+(\.\d+)*$"

    # 用 re.match 函数，用 pattern 匹配 local_version_num
    match_local = re.match(pattern, local_version_num)

    # 如果没有匹配到，就返回2
    if not match_local:
        return 2

    # 用 re.match 函数，尝试用 pattern 匹配 latest_version_num
    match_latest = re.match(pattern, latest_version_num)

    # 如果没有匹配到，就返回2
    if not match_latest:
        return 2

    # 检查两个后缀是否在字典里，如果不在，就修改为“*”
    if local_version_mark not in suffix_dict:
        local_version_mark = "*"
    if latest_version_mark not in suffix_dict:
        latest_version_mark = "*"

    # 变量的长度不一时，自动填充较短的版本
    # 将两个版本号的数字部分用"."分割成列表
    local_version_num_list = local_version_num.split(".")
    latest_version_num_list = latest_version_num.split(".")

    # 计算两个列表的长度
    local_version_num_len = len(local_version_num_list)
    latest_version_num_len = len(latest_version_num_list)

    # 判断两个列表的长度是否相等
    if local_version_num_len == latest_version_num_len:
        # 如果相等，就不需要填充
        pass
    else:
        # 如果不相等，就需要填充，用"0"作为填充的元素
        # 取两个长度的较大值，作为填充的目标长度
        max_len = max(local_version_num_len, latest_version_num_len)
        # 判断哪个列表更短，用一个变量来记录
        if local_version_num_len < latest_version_num_len:
            shorter_list = local_version_num_list
        else:
            shorter_list = latest_version_num_list
        # 循环填充列表，直到达到目标长度
        while len(shorter_list) < max_len:
            # 在列表的末尾添加"0"
            shorter_list.append("0")

    # 用equal_flag来记录两个版本号是否相等
    equal_flag = True

    # 设置默认值
    max_len = 0

    # 循环比较每一段数字的大小，注意要去掉前导零或末尾零
    for i in range(max_len):
        # 将每一段数字转换为整数
        local_version_num_int = int(local_version_num_list[i])
        latest_version_num_int = int(latest_version_num_list[i])
        # 比较两个整数的大小
        if local_version_num_int > latest_version_num_int:
            # 如果local_version_num_int大于latest_version_num_int，就返回1
            return 1
        elif local_version_num_int < latest_version_num_int:
            # 如果local_version_num_int小于latest_version_num_int，就返回-1
            return -1
        elif local_version_num_int == latest_version_num_int:
            # 如果local_version_num_int等于latest_version_num_int，就继续循环
            continue
        else:
            # 其他情况，就返回2
            return 2

    # 循环结束后，判断标志变量的值，如果为True，就认为两个版本号的数字部分相等，需要比较后缀的大小
    if equal_flag:
        if local_version_mark and latest_version_mark:
            # 通过自定义字典比对后缀
            if suffix_dict[local_version_mark] > suffix_dict[latest_version_mark]:
                # 如果local_version_mark大于latest_version_mark，就返回1
                return 1
            elif suffix_dict[local_version_mark] < suffix_dict[latest_version_mark]:
                # 如果local_version_mark小于latest_version_mark，就返回-1
                return -1
            elif suffix_dict[local_version_mark] == suffix_dict[latest_version_mark]:
                # 如果local_version_mark等于latest_version_mark，就需要比较后缀的次级版本大小

                if local_version_mark_num and latest_version_mark_num:

                    # 使用LooseVersion函数来比较两个版本号的后缀的次级版本大小
                    if LooseVersion(local_version_mark_num) > LooseVersion(latest_version_mark_num):
                        # 如果local_version_mark_num大于latest_version_mark_num，就返回1
                        return 1
                    elif LooseVersion(local_version_mark_num) < LooseVersion(latest_version_mark_num):
                        # 如果local_version_mark_num小于latest_version_mark_num，就返回-1
                        return -1
                    elif LooseVersion(local_version_mark_num) == LooseVersion(latest_version_mark_num):
                        # 如果local_version_mark_num等于latest_version_mark_num，就返回0
                        return 0
                    else:
                        # 其他情况，就返回2
                        return 2

                elif local_version_mark_num and not latest_version_mark_num:
                    # 如果只有local_version_mark_num存在，就认为local_version更大，返回1
                    return 1
                elif not local_version_mark_num and latest_version_mark_num:
                    # 如果只有latest_version_mark_num存在，就认为latest_version更大，返回-1
                    return -1
                elif not local_version_mark_num and not latest_version_mark_num:
                    # 如果都没有后缀的次级版本号，就认为两个版本相等，返回0
                    return 0
                else:
                    # 其他情况，就返回2
                    return 2

        elif local_version_mark and not latest_version_mark:
            # 如果只有local_version_mark存在，就认为local_version更大，返回1
            return 1
        elif not local_version_mark and latest_version_mark:
            # 如果只有latest_version_mark存在，就认为latest_version更大，返回-1
            return -1
        elif not local_version_mark and not latest_version_mark:
            # 如果都没有后缀，就认为两个版本相等，返回0
            return 0
        else:
            # 其他情况，就返回2
            return 2
    else:
        # 其他情况，就返回2
        return 2
