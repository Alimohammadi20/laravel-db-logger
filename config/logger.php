<?php
return [
    'enable' => (bool)env('LOGGER_ENABLE', true),
    'middleware' => env('LOGGER_MIDDLEWARE', ''),
    'prefix' => env('LOGGER_PREFIX', 'admin'),
    'asset_url' => env('LOGGER_ASSET_URL', ''),
    // unit: day
    'expire' => (int)env('LOGGER_EXPIRE', '7'),
    'response_time' => [
        'min' => (float)env('LOGGER_MAX_RESPONSE_TIME', '1.0'),
        'max' => (float)env('LOGGER_MIN_RESPONSE_TIME', '0.50'),
    ],
    'show_message_limit' => (int)env('LOGGER_MESSAGE_LIMIT', 250)
];
