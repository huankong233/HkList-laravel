<?php

return [
    'fake_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    'fake_cookie'     => 'BAIDUID=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; BAIDUID_BFESS=A4FDFAE43DDBF7E6956B02F6EF715373:FG=1; newlogin=1',

    'version'        => env('_94LIST_VERSION', '0.0.0'),
    'sleep'          => (int)env('_94LIST_SLEEP', "3"),
    'max_once'       => (int)env('_94LIST_MAX_ONCE', "20"),
    'password'       => env('_94LIST_PASSWORD', ''),
    'announce'       => env('_94LIST_ANNOUNCE', '公告'),
    'user_agent'     => env('_94LIST_USER_AGENT', 'LogStatistic'),
    'need_inv_code'  => (bool)env('_94LIST_NEED_INV_CODE', false),
    'whitelist_mode' => (bool)env('_94LIST_WHITELIST_MODE', false)
];
