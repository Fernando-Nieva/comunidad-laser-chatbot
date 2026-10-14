<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\Exceptions\AIProviderResponseException;
use App\Exceptions\AIProviderTimeoutException;
use App\Exceptions\AIProviderUnavailableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalAIProvider implements AIProvider
{
    private string $baseUrl;
    private string $apiKey;
    private string $model;
    private int $timeout;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        string $model,
        int $timeout = 30
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->timeout = $timeout;
    }

    public function chat(array $messages, array $options = []): string
    {
        if (!$this->apiKey) {
            throw AIProviderUnavailableException::provider('external (falta API key)');
        }

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.3,
            'max_tokens' => $options['max_tokens'] ?? 2000,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiKey)
                ->post("{$this->baseUrl}/chat/completions", $payload);
        } catch (Throwable $e) {
            Log::error('External AI chat request failed', [
                'provider' => 'external',
                'error' => $e->getMessage(),
                'url' => "{$this->baseUrl}/chat/completions",
            ]);
            throw AIProviderTimeoutException::provider('external', $this->timeout);
        }

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error('External AI chat HTTP error', [
                'provider' => 'external',
                'status' => $status,
                'body' => $body,
            ]);
            throw AIProviderResponseException::httpError('external', $status, $body);
        }

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            Log::error('External AI chat malformed response', [
                'provider' => 'external',
                'response' => $data,
            ]);
            throw AIProviderResponseException::malformed('external', 'Falta choices[0].message.content');
        }

        return (string) $data['choices'][0]['message']['content'];
    }

    public function embed(string $text): array
    {
        throw new \BadMethodCallException('Los embeddings siempre se generan con Ollama (local). Use AIService::embed() que usa OllamaProvider.');
    }

    public function isAvailable(): bool
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->withToken($this->apiKey)
                ->get("{$this->baseUrl}/models");
            return $response->successful();
        } catch (Throwable) {
            return false;
        }
    }
}