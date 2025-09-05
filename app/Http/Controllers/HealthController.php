<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app'   => true,
            'db'    => $this->dbOk(),
            'cache' => $this->cacheOk(),
        ];

        $ok = !in_array(false, $checks, true);

        Log::info('healthcheck', ['ok' => $ok, 'checks' => $checks]);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $ok ? 200 : 503);
    }

    private function dbOk(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::select('select 1 as ok');
            return true;
        } catch (\Throwable $e) {
            Log::error('health.db', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function cacheOk(): bool
    {
        try {
            $key = 'health:ping';
            Cache::put($key, 'pong', 5);
            return Cache::get($key) === 'pong';
        } catch (\Throwable $e) {
            Log::error('health.cache', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
