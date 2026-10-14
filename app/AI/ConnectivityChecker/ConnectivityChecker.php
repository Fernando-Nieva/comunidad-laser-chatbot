<?php

namespace App\AI\ConnectivityChecker;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ConnectivityChecker
{
    private int $timeout;
    private string $checkUrl;
    private int $cacheTtl;

    public function __construct(
        string $checkUrl = 'http://www.google.com',
        int $timeout = 2,
        int $cacheTtl = 30
    ) {
        $this->checkUrl = $checkUrl;
        $this->timeout = $timeout;
        $this->cacheTtl = $cacheTtl;
    }

    public static function fromConfig(): self
    {
        return new self(
            checkUrl: config('ai.connectivity_check_url', 'http://www.google.com'),
            timeout: config('ai.connectivity_check_timeout', 2),
            cacheTtl: config('ai.connectivity_cache_ttl', 30),
        );
    }

    public function isOnline(): bool
    {
        return Cache::remember(
            'connectivity_check',
            now()->addSeconds($this->cacheTtl),
            fn() => $this->performCheck()
        );
    }

    private function performCheck(): bool
    {
        try {
            $response = Http::timeout($this->timeout)->head($this->checkUrl);
            $isOnline = $response->successful();

            Log::debug('Connectivity check', [
                'url' => $this->checkUrl,
                'online' => $isOnline,
            ]);

            return $isOnline;
        } catch (\Throwable $e) {
            Log::warning('Connectivity check failed', [
                'url' => $this->checkUrl,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}