<?php

return [
    'version'   => env('_94LIST_VERSION', '0.0.0'),
    'sleep'     => env('_94LIST_SLEEP', 3),
    'maxOnce'   => env('_94LIST_MAX_ONCE', 20),
    'password'  => env('_94LIST_PASSWORD', ''),
    'announce'  => env('_94LIST_ANNOUNCE', '公告'),
    'userAgent' => env('_94LIST_USER_AGENT', 'NetDisk'),
    'limit'     => [
        'size'  => env("_94LIST_LIMIT_SIZE", 10),
        'count' => env("_94LIST_LIMIT_COUNT", 20),
    ],
    'captcha'   => [
        'use'     => env('_94LIST_CAPTCHA_USE', false),
        'VAPTCHA' => [
            'vid' => env('_94LIST_CAPTCHA_VID', ''),
            'key' => env('_94LIST_CAPTCHA_KEY', '')
        ]
    ],
];
