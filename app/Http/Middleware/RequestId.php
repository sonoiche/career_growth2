<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\Uid\Ulid;

class RequestId
{
    public const HEADER = 'X-Request-Id';

    public function handle(Request $request, Closure $next)
    {
        $rid = $request->headers->get(self::HEADER)
            ?: (string) Ulid::generate();

        // Make request id available during the request lifecycle.
        $request->headers->set(self::HEADER, $rid);
        app()->instance('request_id', $rid);

        $response = $next($request);

        // Ensure response carries the same id.
        $response->headers->set(self::HEADER, $rid);

        return $response;
    }
}
