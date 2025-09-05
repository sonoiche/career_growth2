<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{SkillController, RoleController, RoleSkillController};

Route::get('/health', HealthController::class);
Route::get('/version', VersionController::class);

// Ensure all taxonomy read endpoints include X-Taxonomy-Version.
Route::middleware(function (Request $request, \Closure $next) {
    $response = $next($request);
    $response->headers->set('X-Taxonomy-Version', config('taxonomy.version'));
    return $response;
})->group(function () {
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/skills/{skill}', [SkillController::class, 'show']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::get('/roles/{role}/skills', [RoleSkillController::class, 'index']);
});
