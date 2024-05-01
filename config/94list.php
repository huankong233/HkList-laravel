<?php

return [
    'fakeUserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',

    'version'   => env('_94LIST_VERSION', '0.0.0'),
    'sleep'     => (int)env('_94LIST_SLEEP', "3"),
    'maxOnce'   => (int)env('_94LIST_MAX_ONCE', "20"),
    'password'  => env('_94LIST_PASSWORD', ''),
    'announce'  => env('_94LIST_ANNOUNCE', '公告'),
    'userAgent' => env('_94LIST_USER_AGENT', 'LogStatistic')
];
