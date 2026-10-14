<?php

namespace App\Chat;

use App\AI\AIService;
use Illuminate\Support\Facades\Log;

class QueryExtractor
{
    private string $extractPrompt;

    public function __construct(
        private readonly AIService $aiService,
        private readonly int $maxRetries = 1,
    ) {
        $this->extractPrompt = file_get_contents(resource_path('prompts/extract.md'));
    }

    public function extract(array $messages): QueryContext
    {
        $conversationText = $this->formatConversation($messages);

        $promptMessages = [
            ['role' => 'system', 'content' => $this->extractPrompt],
            ['role' => 'user', 'content' => $conversationText],
        ];

        $response = $this->callWithRetry($promptMessages);

        if ($response === null) {
            return new QueryContext();
        }

        return QueryContext::fromArray($response);
    }

    private function formatConversation(array $messages): string
    {
        $parts = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                continue;
            }
            $role = $msg['role'] === 'user' ? 'Usuario' : 'Asistente';
            $parts[] = "{$role}: {$msg['content']}";
        }
        return implode("\n\n", $parts);
    }

    private function callWithRetry(array $messages): ?array
    {
        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $reply = $this->aiService->chat($messages, ['temperature' => 0.1]);
                $json = $this->extractJson($reply);

                if ($json !== null) {
                    return $json;
                }

                Log::warning('QueryExtractor: JSON inválido, reintentando', [
                    'attempt' => $attempt + 1,
                    'response' => $reply,
                ]);
            } catch (\Throwable $e) {
                Log::error('QueryExtractor: Error al extraer contexto', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::warning('QueryExtractor: Fallaron todos los reintentos, devolviendo contexto vacío');
        return null;
    }

    private function extractJson(string $text): ?array
    {
        // Try to find JSON in markdown code blocks
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $text, $matches)) {
            $json = $matches[1];
        } else {
            // Try to find JSON object directly
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $json = $matches[0];
            } else {
                return null;
            }
        }

        $json = trim($json);
        // Handle escaped newlines from markdown code blocks
        $json = stripcslashes($json);
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        // Validate structure - only accept expected keys
        $expectedKeys = ['material', 'thickness_mm', 'machine_power_w', 'job_type', 'machine', 'software'];
        $result = [];
        foreach ($expectedKeys as $key) {
            if (isset($decoded[$key]) && $decoded[$key] !== '') {
                $result[$key] = $decoded[$key];
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }
}