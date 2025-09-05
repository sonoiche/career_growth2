<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class VersionController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $commit = trim((string) @file_get_contents(base_path('.git/HEAD')));
        $ref = null;

        if (str_starts_with($commit, 'ref:')) {
            $path = base_path('.git/' . trim(substr($commit, 4)));
            $ref = @file_get_contents($path);
            $commit = $ref ? trim($ref) : null;
        }

        $version = config('taxonomy.version', date('Y.m.d') . '.0');

        return response()->json([
            'app' => config('app.name'),
            'environment' => app()->environment(),
            'taxonomy_version' => $version,
            'git_commit' => $commit ?: null,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
