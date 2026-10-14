<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\DebugController;
use App\Search\WebSearchProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::redirect('/chat', '/', 301);

Route::get('/health', function () {
    $dbStatus = 'ok';
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'database' => $dbStatus,
        'timestamp' => now()->toISOString(),
    ]);
})->name('health');

Route::post('/api/chat', ChatController::class)
    ->middleware('ratelimit.chat')
    ->name('api.chat');

// Debug route (only in local environment)
Route::get('/debug', DebugController::class)->name('debug');

// Test route for WebSearchProvider binding
Route::get('/test-search-provider', function () {
    config(['ai.search.base_url' => 'https://api.search.example.com']);
    config(['ai.search.api_key' => 'test-key']);
    config(['ai.search.timeout' => 10]);
    
    $provider = app(WebSearchProvider::class);
    
    return response()->json([
        'has_provider' => $provider !== null,
        'provider_class' => $provider ? get_class($provider) : null,
    ]);
});

// Debug route to log manifest requests
Route::get('/build/manifest.json', function () {
    \Log::info('Manifest requested', ['ip' => request()->ip()]);
    return response()->file(public_path('build/manifest.json'));
});