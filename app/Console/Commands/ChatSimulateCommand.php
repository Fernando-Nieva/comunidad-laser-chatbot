<?php

namespace App\Console\Commands;

use App\Chat\ChatOrchestrator;
use App\Chat\ChatReply;
use App\Chat\MissingInfoPolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChatSimulateCommand extends Command
{
    protected $signature = 'chat:simulate 
        {file : Archivo JSON con conversaciones de prueba}
        {--delay=1000 : Delay entre mensajes en ms}
        {--verbose : Mostrar detalles completos}';
    
    protected $description = 'Ejecuta una lista de conversaciones de prueba contra el sistema real';

    public function handle(ChatOrchestrator $orchestrator): int
    {
        $file = $this->argument('file');
        $delay = (int) $this->option('delay');
        $verbose = $this->option('verbose');

        if (!File::exists($file)) {
            $this->error("Archivo no encontrado: {$file}");
            return self::FAILURE;
        }

        $data = json_decode(File::get($file), true);
        
        if (!isset($data['conversations']) || !is_array($data['conversations'])) {
            $this->error('El archivo debe contener un array "conversations"');
            return self::FAILURE;
        }

        $conversationCount = count($data['conversations']);
        $this->info("Simulando {$conversationCount} conversaciones...");
        $this->newLine();

        $results = [];

        foreach ($data['conversations'] as $index => $conversation) {
            $this->info("=== Conversación " . ($index + 1) . ": {$conversation['name']} ===");
            $this->newLine();

            $uuid = null;
            $conversationResults = [];

            foreach ($conversation['messages'] as $msgIndex => $message) {
                $this->info("  [{$msgIndex}] Usuario: {$message}");

                try {
                    // Get or create conversation
                    if ($uuid === null) {
                        $conv = \App\Models\Conversation::create(['uuid' => Str::uuid()]);
                        $uuid = $conv->uuid;
                    }

                    $conversationModel = \App\Models\Conversation::where('uuid', $uuid)->firstOrFail();
                    $reply = $orchestrator->handle($conversationModel, $message);
                    
                    $sourceLabel = match($reply->source) {
                        'community' => '📚 Comunidad',
                        'external_ai' => '🌐 IA Externa',
                        'local_ai' => '💻 IA Local',
                        'question' => '❓ Pregunta',
                        'none' => '❌ Sin info',
                        default => $reply->source,
                    };

                    $this->info("  [{$msgIndex}] Bot ({$sourceLabel}): " . 
                        mb_strimwidth($reply->text, 0, 100, '...'));

                    $conversationResults[] = [
                        'user' => $message,
                        'bot' => $reply->text,
                        'source' => $reply->source,
                        'fallback_level' => $reply->fallbackLevel,
                        'fallback_reason' => $reply->fallbackReason,
                        'scores' => $reply->scores,
                        'experience_ids' => $reply->experienceIds,
                    ];

                    if ($verbose) {
                        $this->line("    Fallback: {$reply->fallbackLevel} ({$reply->fallbackReason ?? 'N/A'})");
                        $this->line("    Scores: " . ($reply->scores ? implode(', ', array_map(fn($s) => number_format($s, 3), $reply->scores)) : 'N/A'));
                        $this->line("    Experience IDs: " . ($reply->experienceIds ? implode(', ', $reply->experienceIds) : 'N/A'));
                    }

                } catch (\Throwable $e) {
                    $this->error("  [{$msgIndex}] Error: " . $e->getMessage());
                    $conversationResults[] = [
                        'user' => $message,
                        'bot' => 'ERROR: ' . $e->getMessage(),
                        'source' => 'error',
                    ];
                }

                usleep($delay * 1000);
            }

            $results[] = [
                'name' => $conversation['name'],
                'messages' => $conversationResults,
            ];

            $this->newLine();
        }

        // Summary
        $this->info('=== RESUMEN ===');
        foreach ($results as $result) {
            $sources = collect($result['messages'])->pluck('source')->countBy();
            $fallbacks = collect($result['messages'])->pluck('fallback_level')->countBy();
            
            $this->info("{$result['name']}:");
            $this->line("  Fuentes: " . $sources->map(fn($c, $s) => "{$s}={$c}")->implode(', '));
            $this->line("  Fallbacks: " . $fallbacks->map(fn($c, $l) => "Nivel {$l}={$c}")->implode(', '));
        }

        // Save report
        $reportFile = storage_path('logs/chat_simulate_' . now()->format('Ymd_His') . '.json');
        File::put($reportFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Reporte guardado en: {$reportFile}");

        return self::SUCCESS;
    }
}