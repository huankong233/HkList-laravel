<?php

return [
    'use'     => env('_94LIST_CAPTCHA_USE', ''),
    'VAPTCHA' => [
        'vid'   => env('_94LIST_CAPTCHA_VAPTCHA_VID', ''),
        'key'   => env('_94LIST_CAPTCHA_VAPTCHA_KEY', ''),
        'scene' => (int)env('_94LIST_CAPTCHA_VAPTCHA_SCENE', "0"),
    ]
];
