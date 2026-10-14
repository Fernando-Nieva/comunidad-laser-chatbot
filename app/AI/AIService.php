<?php

namespace App\AI;

use App\AI\Contracts\AIProvider;
use App\AI\Providers\ExternalAIProvider;
use App\AI\Providers\OllamaProvider;
use App\Exceptions\AIProviderUnavailableException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AIService
{
    private array $providers = [];

    public function __construct(
        private readonly OllamaProvider $ollamaProvider,
        private readonly ?ExternalAIProvider $externalProvider = null
    ) {
        $this->providers['ollama'] = $ollamaProvider;
        if ($externalProvider) {
            $this->providers['external'] = $externalProvider;
        }
    }

    public static function make(): self
    {
        $ollamaConfig = Config::get('ai.ollama');
        $externalConfig = Config::get('ai.external');

        $ollamaProvider = new OllamaProvider(
            $ollamaConfig['base_url'],
            $ollamaConfig['chat_model'],
            $ollamaConfig['embed_model'],
            $ollamaConfig['timeout']
        );

        $externalProvider = null;
        if (!empty($externalConfig['base_url']) && !empty($externalConfig['api_key']) && !empty($externalConfig['model'])) {
            $externalProvider = new ExternalAIProvider(
                $externalConfig['base_url'],
                $externalConfig['api_key'],
                $externalConfig['model'],
                $externalConfig['timeout']
            );
        }

        return new self($ollamaProvider, $externalProvider);
    }

    public function chat(array $messages, array $options = [], ?string $providerName = null): string
    {
        $provider = $this->resolveProvider($providerName);

        if (!$provider->isAvailable()) {
            Log::warning('AI provider not available', ['provider' => $this->getProviderName($provider)]);
            throw AIProviderUnavailableException::provider($this->getProviderName($provider));
        }

        try {
            $start = microtime(true);
            $response = $provider->chat($messages, $options);
            $latency = round((microtime(true) - $start) * 1000);

            Log::info('AI chat completed', [
                'provider' => $this->getProviderName($provider),
                'latency_ms' => $latency,
                'messages_count' => count($messages),
            ]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('AI chat failed', [
                'provider' => $this->getProviderName($provider),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function embed(string $text): array
    {
        return $this->ollamaProvider->embed($text);
    }

    public function provider(string $name): AIProvider
    {
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException("Proveedor de IA no registrado: {$name}");
        }
        return $this->providers[$name];
    }

    public function getDefaultProviderName(): string
    {
        return Config::get('ai.provider', 'ollama');
    }

    private function resolveProvider(?string $providerName = null): AIProvider
    {
        $name = $providerName ?? $this->getDefaultProviderName();

        if (!isset($this->providers[$name])) {
            Log::warning('Requested AI provider not configured, falling back to ollama', [
                'requested' => $name,
                'available' => array_keys($this->providers),
            ]);
            $name = 'ollama';
        }

        return $this->providers[$name];
    }

    private function getProviderName(AIProvider $provider): string
    {
        foreach ($this->providers as $name => $p) {
            if ($p === $provider) {
                return $name;
            }
        }
        return 'unknown';
    }
}