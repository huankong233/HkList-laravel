<?php

return [
    'version'   => env('_94LIST_VERSION', '0.0.0'),
    'sleep'     => (int)env('_94LIST_SLEEP', "3"),
    'maxOnce'   => (int)env('_94LIST_MAX_ONCE', "20"),
    'password'  => env('_94LIST_PASSWORD', ''),
    'announce'  => env('_94LIST_ANNOUNCE', '公告'),
    'userAgent' => env('_94LIST_USER_AGENT', 'NetDisk'),
    'captcha'   => [
        'use'     => env('_94LIST_CAPTCHA_USE', ''),
        'VAPTCHA' => [
            'vid'   => env('_94LIST_CAPTCHA_VAPTCHA_VID', ''),
            'key'   => env('_94LIST_CAPTCHA_VAPTCHA_KEY', ''),
            'scene' => (int)env('_94LIST_CAPTCHA_VAPTCHA_SCENE', "0"),
        ]
    ],
];
