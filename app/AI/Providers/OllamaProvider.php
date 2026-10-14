<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\Exceptions\AIProviderResponseException;
use App\Exceptions\AIProviderTimeoutException;
use App\Exceptions\AIProviderUnavailableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OllamaProvider implements AIProvider
{
    private string $baseUrl;
    private string $chatModel;
    private string $embedModel;
    private int $timeout;

    public function __construct(
        string $baseUrl,
        string $chatModel,
        string $embedModel,
        int $timeout = 60
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->chatModel = $chatModel;
        $this->embedModel = $embedModel;
        $this->timeout = $timeout;
    }

    public function chat(array $messages, array $options = []): string
    {
        $payload = [
            'model' => $this->chatModel,
            'messages' => $messages,
            'stream' => false,
            'options' => array_merge([
                'temperature' => 0.3,
                'top_p' => 0.9,
            ], $options),
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/chat", $payload);
        } catch (Throwable $e) {
            Log::error('Ollama chat request failed', [
                'provider' => 'ollama',
                'error' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/chat",
            ]);
            throw AIProviderTimeoutException::provider('ollama', $this->timeout);
        }

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error('Ollama chat HTTP error', [
                'provider' => 'ollama',
                'status' => $status,
                'body' => $body,
            ]);
            throw AIProviderResponseException::httpError('ollama', $status, $body);
        }

        $data = $response->json();

        if (!isset($data['message']['content'])) {
            Log::error('Ollama chat malformed response', [
                'provider' => 'ollama',
                'response' => $data,
            ]);
            throw AIProviderResponseException::malformed('ollama', 'Falta message.content');
        }

        return (string) $data['message']['content'];
    }

    public function embed(string $text): array
    {
        $payload = [
            'model' => $this->embedModel,
            'input' => $text,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/embeddings", $payload);
        } catch (Throwable $e) {
            Log::error('Ollama embed request failed', [
                'provider' => 'ollama',
                'error' => $e->getMessage(),
                'url' => "{$this->baseUrl}/api/embeddings",
            ]);
            throw AIProviderTimeoutException::provider('ollama', $this->timeout);
        }

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error('Ollama embed HTTP error', [
                'provider' => 'ollama',
                'status' => $status,
                'body' => $body,
            ]);
            throw AIProviderResponseException::httpError('ollama', $status, $body);
        }

        $data = $response->json();

        if (!isset($data['embedding']) || !is_array($data['embedding'])) {
            Log::error('Ollama embed malformed response', [
                'provider' => 'ollama',
                'response' => $data,
            ]);
            throw AIProviderResponseException::malformed('ollama', 'Falta embedding array');
        }

        return $data['embedding'];
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (Throwable) {
            return false;
        }
    }
}