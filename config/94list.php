<?php

return [
    'fake_user_agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    'fake_wx_user_agent' => 'Mozilla/5.0 (Linux; Android 7.1.1; MI 6 Build/NMF26X; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.132 MQQBrowser/6.2 TBS/043807 Mobile Safari/537.36 MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN MicroMessenger/6.6.1.1220(0x26060135) NetType/4G Language/zh_CN miniProgram',
    'fake_cookie'        => 'BAIDUID=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; BAIDUID_BFESS=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; newlogin=1',

    'version'        => env('_94LIST_VERSION', '0.0.0'),
    'sleep'          => (int)env('_94LIST_SLEEP', "3"),
    'max_once'       => (int)env('_94LIST_MAX_ONCE', "20"),
    'password'       => env('_94LIST_PASSWORD', ''),
    'announce'       => env('_94LIST_ANNOUNCE', '公告'),
    'user_agent'     => env('_94LIST_USER_AGENT', 'LogStatistic'),
    'need_inv_code'  => (bool)env('_94LIST_NEED_INV_CODE', false),
    'whitelist_mode' => (bool)env('_94LIST_WHITELIST_MODE', false)
];
