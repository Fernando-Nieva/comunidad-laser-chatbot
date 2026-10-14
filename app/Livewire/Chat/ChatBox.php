<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatBox extends Component
{
    public array $messages = [];
    public string $input = '';
    public bool $isLoading = false;
    public ?string $conversationUuid = null;

    public function send(): void
    {
        $message = trim($this->input);
        if ($message === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $message];
        $this->input = '';
        $this->isLoading = true;

        try {
            $response = $this->callApi($message);
            
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['reply'] ?? $response,
                'source' => $response['source'] ?? 'local_ai',
                'fallback_level' => $response['fallback_level'] ?? 0,
                'fallback_reason' => $response['fallback_reason'] ?? null,
                'scores' => $response['scores'] ?? [],
                'experience_ids' => $response['experience_ids'] ?? [],
            ];
        } catch (\Throwable) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Ups, tuve un problema al consultar la IA. Probá de nuevo en un ratito.',
                'source' => 'none',
                'fallback_level' => 3,
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function newConversation(): void
    {
        $this->messages = [];
        $this->conversationUuid = null;
        $this->input = '';
    }

    private function callApi(string $message): array
    {
        $payload = ['message' => $message];
        if ($this->conversationUuid) {
            $payload['conversation_uuid'] = $this->conversationUuid;
        }

        $response = app('http')->post(url('/api/chat'), [
            'json' => $payload,
            'timeout' => 60,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('API error: ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['conversation_uuid'])) {
            $this->conversationUuid = $data['conversation_uuid'];
        }

        return $data;
    }

public function getSourceLabel(string $source, array $experienceIds = [], array $webSourceUrls = []): string
    {
        return match ($source) {
            'community' => '📚 Basado en experiencias de la comunidad' .
                (!empty($experienceIds) ? ' (IDs: ' . implode(', ', $experienceIds) . ')' : ''),
            'external_ai' => '🌐 Orientación de IA externa',
            'local_ai' => '💻 Orientación de IA local',
            'external_ai_with_web' => '🌐 Información de fuentes externas' .
                (!empty($webSourceUrls) ? ' (' . count($webSourceUrls) . ' fuentes)' : ''),
            'local_ai_with_web' => '💻 Información de fuentes externas (IA local)' .
                (!empty($webSourceUrls) ? ' (' . count($webSourceUrls) . ' fuentes)' : ''),
            'question' => '❓ Pregunta para completar información',
            'none' => '❌ Sin información suficiente',
            default => '📝 Respuesta',
        };
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}