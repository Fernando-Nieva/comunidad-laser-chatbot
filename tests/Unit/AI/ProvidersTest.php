<?php

use App\AI\AIService;
use App\AI\Providers\OllamaProvider;
use App\AI\Providers\ExternalAIProvider;
use App\Exceptions\AIProviderResponseException;
use App\Exceptions\AIProviderTimeoutException;
use App\Exceptions\AIProviderUnavailableException;
use Illuminate\Support\Facades\Http;

test('OllamaProvider chat returns response on success', function () {
    Http::fake([
        'http://localhost:11434/api/chat' => Http::response([
            'message' => ['content' => 'Hola, ¿en qué puedo ayudarte?'],
        ], 200),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $response = $provider->chat([
        ['role' => 'user', 'content' => 'Hola'],
    ]);

    expect($response)->toBe('Hola, ¿en qué puedo ayudarte?');
});

test('OllamaProvider embed returns vector on success', function () {
    Http::fake([
        'http://localhost:11434/api/embeddings' => Http::response([
            'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
        ], 200),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $embedding = $provider->embed('texto de prueba');

    expect($embedding)->toBeArray()
        ->toHaveCount(5)
        ->toBe([0.1, 0.2, 0.3, 0.4, 0.5]);
});

test('OllamaProvider isAvailable returns true when tags endpoint works', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    expect($provider->isAvailable())->toBeTrue();
});

test('OllamaProvider isAvailable returns false when tags endpoint fails', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response([], 500),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    expect($provider->isAvailable())->toBeFalse();
});

test('OllamaProvider throws on chat timeout', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('timeout');
    });

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderTimeoutException::class);

test('OllamaProvider throws on chat HTTP error', function () {
    Http::fake([
        'http://localhost:11434/api/chat' => Http::response(['error' => 'model not found'], 404),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderResponseException::class);

test('OllamaProvider throws on malformed chat response', function () {
    Http::fake([
        'http://localhost:11434/api/chat' => Http::response(['wrong' => 'structure'], 200),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderResponseException::class);

test('OllamaProvider throws on embed timeout', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('timeout');
    });

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $provider->embed('test');
})->throws(AIProviderTimeoutException::class);

test('OllamaProvider throws on malformed embed response', function () {
    Http::fake([
        'http://localhost:11434/api/embeddings' => Http::response(['no' => 'embedding'], 200),
    ]);

    $provider = new OllamaProvider(
        'http://localhost:11434',
        'qwen2.5:7b',
        'bge-m3',
        30
    );

    $provider->embed('test');
})->throws(AIProviderResponseException::class);

test('ExternalAIProvider chat returns response on success', function () {
    Http::fake([
        'https://api.external.com/chat/completions' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Respuesta externa']],
            ],
        ], 200),
    ]);

    $provider = new ExternalAIProvider(
        'https://api.external.com',
        'test-key',
        'gpt-4',
        30
    );

    $response = $provider->chat([
        ['role' => 'user', 'content' => 'Hola'],
    ]);

    expect($response)->toBe('Respuesta externa');
});

test('ExternalAIProvider isAvailable returns true when models endpoint works', function () {
    Http::fake([
        'https://api.external.com/models' => Http::response(['data' => []], 200),
    ]);

    $provider = new ExternalAIProvider(
        'https://api.external.com',
        'test-key',
        'gpt-4',
        30
    );

    expect($provider->isAvailable())->toBeTrue();
});

test('ExternalAIProvider isAvailable returns false when no API key', function () {
    $provider = new ExternalAIProvider(
        'https://api.external.com',
        '',
        'gpt-4',
        30
    );

    expect($provider->isAvailable())->toBeFalse();
});

test('ExternalAIProvider throws on chat without API key', function () {
    $provider = new ExternalAIProvider(
        'https://api.external.com',
        '',
        'gpt-4',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderUnavailableException::class);

test('ExternalAIProvider embed throws BadMethodCallException', function () {
    $provider = new ExternalAIProvider(
        'https://api.external.com',
        'test-key',
        'gpt-4',
        30
    );

    $provider->embed('test');
})->throws(\BadMethodCallException::class, 'Los embeddings siempre se generan con Ollama');

test('ExternalAIProvider throws on chat HTTP error', function () {
    Http::fake([
        'https://api.external.com/chat/completions' => Http::response(['error' => 'unauthorized'], 401),
    ]);

    $provider = new ExternalAIProvider(
        'https://api.external.com',
        'test-key',
        'gpt-4',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderResponseException::class);

test('ExternalAIProvider throws on malformed chat response', function () {
    Http::fake([
        'https://api.external.com/chat/completions' => Http::response(['wrong' => 'structure'], 200),
    ]);

    $provider = new ExternalAIProvider(
        'https://api.external.com',
        'test-key',
        'gpt-4',
        30
    );

    $provider->chat([['role' => 'user', 'content' => 'test']]);
})->throws(AIProviderResponseException::class);

test('AIService resolves default provider from config', function () {
    config(['ai.provider' => 'ollama']);

    Http::fake([
        'http://localhost:11434/api/chat' => Http::response([
            'message' => ['content' => 'pong'],
        ], 200),
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    ]);

    $service = AIService::make();

    expect($service->getDefaultProviderName())->toBe('ollama');
});

test('AIService chat uses default provider when none specified', function () {
    config(['ai.provider' => 'ollama']);

    Http::fake([
        'http://localhost:11434/api/chat' => Http::response([
            'message' => ['content' => 'pong'],
        ], 200),
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    ]);

    $service = AIService::make();
    $response = $service->chat([['role' => 'user', 'content' => 'ping']]);

    expect($response)->toBe('pong');
});

test('AIService chat can use specific provider by name', function () {
    config([
        'ai.provider' => 'ollama',
        'ai.external' => [
            'base_url' => 'https://api.external.com',
            'api_key' => 'test-key',
            'model' => 'gpt-4',
            'timeout' => 30,
        ],
    ]);

    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
        'https://api.external.com/models' => Http::response(['data' => []], 200),
        'https://api.external.com/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => 'external response']]],
        ], 200),
    ]);

    $service = AIService::make();
    $response = $service->chat(
        [['role' => 'user', 'content' => 'ping']],
        [],
        'external'
    );

    expect($response)->toBe('external response');
});

test('AIService embed always uses Ollama', function () {
    config(['ai.provider' => 'ollama']);

    Http::fake([
        'http://localhost:11434/api/embeddings' => Http::response([
            'embedding' => [0.1, 0.2, 0.3],
        ], 200),
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    ]);

    $service = AIService::make();
    $embedding = $service->embed('test');

    expect($embedding)->toBeArray()->toHaveCount(3);
});

test('AIService provider() returns specific provider', function () {
    config(['ai.provider' => 'ollama']);

    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    ]);

    $service = AIService::make();
    $provider = $service->provider('ollama');

    expect($provider)->toBeInstanceOf(OllamaProvider::class);
});

test('AIService provider() throws for unknown provider', function () {
    config(['ai.provider' => 'ollama']);

    $service = AIService::make();

    $service->provider('unknown');
})->throws(\InvalidArgumentException::class, 'Proveedor de IA no registrado: unknown');