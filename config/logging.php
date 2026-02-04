<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK_CHANNELS', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
        | daily: log tổng hợp, xoay theo ngày. Production: đặt LOG_LEVEL=warning hoặc error.
        */
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => (int) env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        /*
        | Production: stack = daily + error_daily.
        | - daily: warning/error trở lên, 14 ngày.
        | - error_daily: chỉ error+, 30 ngày (dễ đọc, monitor, alert).
        */
        'production' => [
            'driver' => 'stack',
            'channels' => ['daily', 'error_daily'],
            'ignore_exceptions' => false,
        ],

        'error_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel-error.log'),
            'level' => 'error',
            'days' => (int) env('LOG_ERROR_DAYS', 30),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

    ],

];
