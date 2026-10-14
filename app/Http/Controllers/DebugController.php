<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DebugController extends Controller
{
    public function __construct()
    {
        // Only allow in local environment
        if (app()->environment('production')) {
            abort(404);
        }
    }

    public function __invoke(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request)
    {
        $conversationUuid = $request->query('conversation_uuid');
        
        $query = Conversation::with(['messages' => function ($q) {
            $q->orderBy('created_at');
        }]);

        if ($conversationUuid) {
            $query->where('uuid', $conversationUuid);
        }

        $conversations = $query->latest('updated_at')->take(20)->get();

        $debugData = [];
        
        foreach ($conversations as $conv) {
            $lastUserMessage = $conv->messages()
                ->where('role', 'user')
                ->latest('created_at')
                ->first();

            $lastAssistantMessage = $conv->messages()
                ->where('role', 'assistant')
                ->latest('created_at')
                ->first();

            // Extract QueryContext from last user message metadata
            $queryContext = null;
            if ($lastUserMessage && $lastUserMessage->metadata) {
                $queryContext = \App\Chat\QueryContext::fromArray($lastUserMessage->metadata);
            }

            // Get source info from last assistant message
            $source = $lastAssistantMessage?->metadata['source'] ?? 'unknown';
            $fallbackLevel = $lastAssistantMessage?->metadata['fallback_level'] ?? 0;
            $fallbackReason = $lastAssistantMessage?->metadata['fallback_reason'];
            $scores = $lastAssistantMessage?->metadata['scores'] ?? [];
            $experienceIds = $lastAssistantMessage?->metadata['experience_ids'] ?? [];
            $sourceLabel = $this->getSourceLabel($source, $experienceIds);

            $debugData[] = [
                'conversation_uuid' => $conv->uuid,
                'created_at' => $conv->created_at?->toISOString(),
                'updated_at' => $conv->updated_at?->toISOString(),
                'message_count' => $conv->messages()->count(),
                'last_user_message' => $lastUserMessage?->content,
                'last_user_message_at' => $lastUserMessage?->created_at?->toISOString(),
                'query_context' => $queryContext?->toArray(),
                'last_response' => [
                    'content' => $lastAssistantMessage?->content,
                    'source' => $source,
                    'source_label' => $sourceLabel,
                    'fallback_level' => $fallbackLevel,
                    'fallback_reason' => $fallbackReason,
                    'scores' => $scores,
                    'experience_ids' => $experienceIds,
                    'at' => $lastAssistantMessage?->created_at?->toISOString(),
                ],
                'all_messages' => $conv->messages->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content,
                        'metadata' => $msg->metadata,
                        'created_at' => $msg->created_at?->toISOString(),
                    ];
                })->toArray(),
            ];
        }

        // Also include system config
        $config = [
            'rag' => config('rag'),
            'ai' => config('ai'),
            'chat' => config('chat'),
            'safety' => array_keys(config('safety.dangerous_materials', [])),
        ];

        return response()->json([
            'environment' => app()->environment(),
            'conversations' => $debugData,
            'config' => $config,
            'missing_info_policy' => [
                'required_fields' => config('rag.required_fields'),
                'recommended_fields' => config('rag.recommended_fields'),
                'max_questions_in_row' => config('rag.max_questions_in_row'),
                'filter_by_material' => config('rag.filter_by_material'),
                'filter_by_job_type' => config('rag.filter_by_job_type'),
                'require_material_match' => config('rag.require_material_match'),
            ],
            'connectivity' => [
                'is_online' => App::make(\App\AI\ConnectivityChecker\ConnectivityChecker::class)->isOnline(),
            ],
        ]);
    }

    private function getSourceLabel(string $source, array $experienceIds): string
    {
        return match ($source) {
            'community' => '📚 Basado en experiencias de la comunidad' . 
                (!empty($experienceIds) ? ' (IDs: ' . implode(', ', $experienceIds) . ')' : ''),
            'external_ai' => '🌐 Orientación de IA externa',
            'local_ai' => '💻 Orientación de IA local',
            'question' => '❓ Pregunta para completar información',
            'none' => '❌ Sin información suficiente',
            default => '📝 Respuesta',
        };
    }
}