<?php
return [
    'enable' => (bool)env('DBLOGGER_ENABLE', true),
    'middleware' => env('DBLOGGER_MIDDLEWARE', ''),
    'prefix' => env('DBLOGGER_PREFIX', 'admin'),
    'asset_url' => env('DBLOGGER_ASSET_URL', ''),
    // unit: day
    'expire' => (int)env('DBLOGGER_EXPIRE', '7'),
    'response_time' => [
        'min' => (float)env('DBLOGGER_MAX_RESPONSE_TIME', '1.0'),
        'max' => (float)env('DBLOGGER_MIN_RESPONSE_TIME', '0.50'),
    ],
    'show_message_limit' => (int)env('DBLOGGER_MESSAGE_LIMIT', 250)
];
