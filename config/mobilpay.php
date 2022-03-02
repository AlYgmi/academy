<?php

return [
    'merchant_id' => env('MOBILPAY_MERCHANT_ID'),
    'public_key_path' => env('MOBILPAY_PUBLIC_KEY_PATH'),
    'private_key_path' => env('MOBILPAY_PRIVATE_KEY_PATH'),
    'testMode' => env('MOBILPAY_TEST_MODE'),
    'currency' => 'RON',
    'confirm_url' => env('MOBILPAY_CONFIRM_URL'),
    'cancel_url' => env('MOBILPAY_CANCEL_URL'),
    'return_url' => env('MOBILPAY_RETURN_URL'),
];
