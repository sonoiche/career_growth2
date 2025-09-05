<?php

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily-json'],
            'ignore_exceptions' => false,
        ],

        'daily-json' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => storage_path('logs/laravel.json.log'),
            ],
            'level' => env('LOG_LEVEL', 'debug'),
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'append_newline' => true,
                'include_stacktraces' => true,
            ],
        ],

        // Keep stderr for local dev / containers
        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => ['stream' => 'php://stderr'],
            'formatter' => JsonFormatter::class,
            'formatter_with' => ['append_newline' => true],
        ],
    ],
];
