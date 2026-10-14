<?php

namespace App\Console\Commands;

use App\AI\AIService;
use App\Models\Experience;
use App\Models\ExperienceEmbedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RagEmbedCommand extends Command
{
    protected $signature = 'rag:embed {--fresh : Re-generate all embeddings even if they exist}';
    protected $description = 'Genera embeddings para experiencias aprobadas';

    public function handle(AIService $aiService): int
    {
        $fresh = $this->option('fresh');

        $query = Experience::where('status', 'approved')
            ->with(['material', 'jobType', 'parameters']);

        if (!$fresh) {
            // Only experiences without embedding or with changed source text
            $query->whereDoesntHave('embedding')
                ->orWhereHas('embedding', function ($q) {
                    $q->whereRaw('source_text != ?', [new \App\Models\Experience()]); // We'll handle this differently
                });
        }

        $experiences = $query->get();

        if ($experiences->isEmpty()) {
            $this->info('No hay experiencias para procesar.');
            return self::SUCCESS;
        }

        $this->info("Procesando {$experiences->count()} experiencias...");

        $progressBar = $this->output->createProgressBar($experiences->count());
        $progressBar->start();

        $processed = 0;
        $errors = 0;

        foreach ($experiences as $experience) {
            try {
                $knowledgeText = $experience->getKnowledgeText();

                // Check if embedding already exists and source text hasn't changed
                $existingEmbedding = $experience->embedding;
                if (!$fresh && $existingEmbedding && $existingEmbedding->source_text === $knowledgeText) {
                    $progressBar->advance();
                    continue;
                }

                $embedding = $aiService->embed($knowledgeText);

                ExperienceEmbedding::updateOrCreate(
                    ['experience_id' => $experience->id],
                    [
                        'embedding' => $embedding,
                        'source_text' => $knowledgeText,
                        'model' => config('ai.ollama.embed_model', 'bge-m3'),
                    ]
                );

                $processed++;
            } catch (\Throwable $e) {
                $errors++;
                Log::error('RAG Embed: Error procesando experiencia', [
                    'experience_id' => $experience->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("\nError en experiencia {$experience->id}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Completado: {$processed} procesadas, {$errors} errores.");

        return self::SUCCESS;
    }
}