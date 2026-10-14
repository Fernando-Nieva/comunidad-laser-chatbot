<?php

namespace App\Http\Controllers\Api;

use App\Chat\ChatService;
use App\Chat\InputGuardrails;
use App\Chat\PostProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatController
{
    public function __construct(
        private readonly ChatService $chatService,
        private readonly InputGuardrails $inputGuardrails,
        private readonly PostProcessor $postProcessor,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => ['required', 'string', 'max:1000'],
                'conversation_uuid' => ['nullable', 'string', 'uuid'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Mensaje inválido',
                'details' => $e->errors(),
            ], 422);
        }

        // Input guardrails
        $guardrailError = $this->inputGuardrails->validate($validated['message']);
        if ($guardrailError !== null) {
            return response()->json([
                'error' => $guardrailError,
            ], 400);
        }

        try {
            $result = $this->chatService->ask(
                $validated['message'],
                $validated['conversation_uuid'] ?? null
            );

            // Post-process the reply
            $processedReply = $this->postProcessor->process(
                $result['reply'],
                $result['source'] ?? 'local_ai',
                $result['experience_ids'] ?? [],
                $result['scores'] ?? []
            );

            return response()->json([
                'reply' => $processedReply,
                'conversation_uuid' => $result['conversation_uuid'],
                'source' => $result['source'] ?? 'local_ai',
                'fallback_level' => $result['fallback_level'] ?? 0,
                'fallback_reason' => $result['fallback_reason'] ?? null,
                'scores' => $result['scores'] ?? [],
                'experience_ids' => $result['experience_ids'] ?? [],
                'web_source_urls' => $result['web_source_urls'] ?? [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No pude procesar tu consulta en este momento. Probá de nuevo más tarde.',
            ], 500);
        }
    }
}