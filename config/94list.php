<?php

return [
    "fake_user_agent"    => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "fake_wx_user_agent" => "Mozilla/5.0 (Linux; Android 7.1.1; MI 6 Build/NMF26X; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.132 MQQBrowser/6.2 TBS/043807 Mobile Safari/537.36 MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN miniProgram",
    "fake_cookie"        => "BAIDUID=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; BAIDUID_BFESS=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; newlogin=1",

    "version"        => "1.3.19",
    "sleep"          => (int)env("_94LIST_SLEEP", 3),
    "max_once"       => (int)env("_94LIST_MAX_ONCE", 20),
    "password"       => env("_94LIST_PASSWORD", ""),
    "announce"       => env("_94LIST_ANNOUNCE", "公告"),
    "user_agent"     => env("_94LIST_USER_AGENT", "netdisk;7.42.0.5;PC"),
    "need_inv_code"  => (bool)env("_94LIST_NEED_INV_CODE", true),
    "whitelist_mode" => (bool)env("_94LIST_WHITELIST_MODE", false),

    "show_copyright"   => (bool)env("_94LIST_SHOW_COPYRIGHT", true),
    "custom_copyright" => env("_94LIST_CUSTOM_COPYRIGHT", "本项目半开源, 项目地址: https://github.com/huankong233/94list-laravel"),

    "main_server" => env("_94LIST_MAIN_SERVER", "空"),
    "code"        => env("_94LIST_CODE", "空"),

    "parse_mode"      => (int)env("_94LIST_PARSE_MODE", 3),
    "max_filesize"    => (int)env("_94LIST_MAX_FILESIZE", 536870912000),
    "min_single_file" => (int)env("_94LIST_MIN_SINGLE_FILESIZE", 0),
    "token_mode"      => (bool)env("_94LIST_TOKEN_MODE", true),
    "button_link"     => env("_94LIST_BUTTON_LINK", ""),
    "limit_cn"        => (bool)env("_94LIST_LIMIT_CN", true),
    "limit_prov"      => (bool)env("_94LIST_LIMIT_PROV", false),

    "show_login_button" => (bool)env("_94LIST_SHOW_LOGIN_BUTTON", true),
    "token_bind_ip"     => (bool)env("_94LIST_TOKEN_BIND_IP", false)
];
