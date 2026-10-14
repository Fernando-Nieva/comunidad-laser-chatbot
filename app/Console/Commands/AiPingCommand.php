<?php

namespace App\Console\Commands;

use App\AI\AIService;
use Illuminate\Console\Command;

class AiPingCommand extends Command
{
    protected $signature = 'ai:ping {--provider= : Proveedor específico a probar (ollama|external)}';
    protected $description = 'Prueba la conectividad con el proveedor de IA configurado';

    public function handle(AIService $aiService): int
    {
        $providerName = $this->option('provider') ?: $aiService->getDefaultProviderName();

        $this->info("Probando proveedor: {$providerName}");

        try {
            $provider = $aiService->provider($providerName);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        if (!$provider->isAvailable()) {
            $this->error("Proveedor {$providerName} no disponible");
            return self::FAILURE;
        }

        $this->info('Proveedor disponible. Enviando mensaje de prueba...');

        $messages = [
            ['role' => 'user', 'content' => 'Responde solo: pong'],
        ];

        $start = microtime(true);
        try {
            $response = $aiService->chat($messages, [], $providerName);
            $latency = round((microtime(true) - $start) * 1000);
        } catch (\Throwable $e) {
            $this->error("Error en chat: {$e->getMessage()}");
            return self::FAILURE;
        }

        $this->info("Respuesta: {$response}");
        $this->info("Latencia: {$latency} ms");

        // Test embeddings (solo Ollama)
        if ($providerName === 'ollama') {
            $this->info('Probando embeddings...');
            $start = microtime(true);
            try {
                $embedding = $aiService->embed('test embedding');
                $latency = round((microtime(true) - $start) * 1000);
                $this->info("Embedding generado: " . count($embedding) . " dimensiones");
                $this->info("Latencia embedding: {$latency} ms");
            } catch (\Throwable $e) {
                $this->error("Error en embedding: {$e->getMessage()}");
                return self::FAILURE;
            }
        }

        $this->info('¡Ping exitoso!');
        return self::SUCCESS;
    }
}