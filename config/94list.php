<?php

return [
    'ssl'            => env("APP_SSL", false),
    'prefix'         => env("ADMIN_ROUTE_PREFIX", "/admin"),
    'user_agent'     => env("_94LIST_UA", 'netpan'),
    'announceSwitch' => env("_94LIST_ANNOUNCESWITCH", true),
    'announce'       => env("_94LIST_ANNOUNCE", '公告'),
    'version'        => env("_94LIST_VERSION", '1.0.0'),
    'cookie'         => env("_94LIST_COOKIE", ""),
    'sleep'          => env("_94LIST_SLEEP", 3),
    'max_once'       => env("_94LIST_MAXONCE", 20)
];
