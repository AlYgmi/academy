<?php

return [
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
    'for_login' => env('NOCAPTCHA_FOR_LOGIN', 'false'),
    'for_reg' => env('NOCAPTCHA_FOR_REG', 'false'),
    'for_contact' => env('NOCAPTCHA_FOR_CONTACT', 'false'),
    'is_invisible' => env('NOCAPTCHA_IS_INVISIBLE', 'false'),
];
