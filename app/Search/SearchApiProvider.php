<?php

namespace App\Search;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SearchApiProvider implements WebSearchProvider
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        int $timeout = 10
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
    }

    public function search(string $query, int $limit = 5): array
    {
        if (!$this->apiKey) {
            Log::warning('Web search requested but no API key configured');
            return [];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiKey)
                ->post("{$this->baseUrl}/search", [
                    'query' => $query,
                    'limit' => $limit,
                ]);
        } catch (Throwable $e) {
            Log::error('Web search request failed', [
                'provider' => 'search_api',
                'error' => $e->getMessage(),
                'query' => $query,
            ]);
            return [];
        }

        if ($response->failed()) {
            Log::error('Web search HTTP error', [
                'provider' => 'search_api',
                'status' => $response->status(),
                'body' => $response->body(),
                'query' => $query,
            ]);
            return [];
        }

        $data = $response->json();

        if (!isset($data['results']) || !is_array($data['results'])) {
            Log::error('Web search malformed response', [
                'provider' => 'search_api',
                'response' => $data,
                'query' => $query,
            ]);
            return [];
        }

        $results = [];
        foreach ($data['results'] as $result) {
            if (isset($result['title'], $result['url'], $result['snippet'])) {
                $results[] = [
                    'title' => (string) $result['title'],
                    'url' => (string) $result['url'],
                    'snippet' => (string) $result['snippet'],
                ];
            }
        }

        return $results;
    }

    public function isAvailable(): bool
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->withToken($this->apiKey)
                ->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (Throwable) {
            return false;
        }
    }
}