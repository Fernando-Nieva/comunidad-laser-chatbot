<?php

namespace App\Chat;

use App\Chat\Orchestrator\ChatOrchestrator;
use App\Models\Conversation;
use App\Models\Message;

class ChatService
{
    public function __construct(
        private readonly ChatOrchestrator $orchestrator,
    ) {}

    public function ask(string $message, ?string $conversationUuid = null): array
    {
        $conversation = $this->getOrCreateConversation($conversationUuid);

        // Save user message
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);

        // Delegate to orchestrator
        $reply = $this->orchestrator->handle($conversation, $message);

        // If orchestrator returned a question, it already saved the assistant message
        // For other cases, we need to save the reply
        if ($reply->source !== 'question') {
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $reply->text,
                'metadata' => $reply->toArray(),
            ]);
        }

        return [
            'reply' => $reply->text,
            'conversation_uuid' => $conversation->uuid,
            'source' => $reply->source,
            'fallback_level' => $reply->fallbackLevel,
            'fallback_reason' => $reply->fallbackReason,
            'scores' => $reply->scores,
            'experience_ids' => $reply->experienceIds,
            'web_source_urls' => $reply->webSourceUrls,
        ];
    }

    public function newConversation(): Conversation
    {
        return Conversation::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
        ]);
    }

    private function getOrCreateConversation(?string $conversationUuid): Conversation
    {
        if ($conversationUuid) {
            $conversation = Conversation::where('uuid', $conversationUuid)->first();
            if ($conversation) {
                return $conversation;
            }
        }

        return $this->newConversation();
    }
}