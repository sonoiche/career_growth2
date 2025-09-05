<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Attach processors globally for richer JSON logs
        foreach (['stack', 'daily', 'stderr'] as $channel) {
            try {
                $logger = logger()->channel($channel)->getLogger();
                $logger->pushProcessor(new WebProcessor());
                $logger->pushProcessor(function (array $record) {
                    $record['extra']['request_id'] = app()->has('request_id') ? app('request_id') : null;
                    return $record;
                });
                $logger->pushProcessor(new UidProcessor());
            } catch (\Throwable $e) {
                // Avoid boot failures if a channel isn't configured yet
            }
        }
    }
}
