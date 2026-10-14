<?php

namespace App\RAG;

use App\AI\AIService;
use App\Chat\QueryContext;
use App\Models\Experience;
use App\Models\ExperienceEmbedding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Retriever
{
    private int $topK;
    private float $relevanceThreshold;
    private int $minResults;
    private bool $filterByMaterial;
    private bool $filterByJobType;

    public function __construct(
        private readonly AIService $aiService,
        private readonly SimilarityCalculator $similarityCalculator,
    ) {
        $this->topK = config('rag.top_k', 5);
        $this->relevanceThreshold = config('rag.relevance_threshold', 0.75);
        $this->minResults = config('rag.min_results', 2);
        $this->filterByMaterial = config('rag.filter_by_material', true);
        $this->filterByJobType = config('rag.filter_by_job_type', true);
    }

    public function retrieve(string $queryText, ?QueryContext $context = null): Collection
    {
        // Get query embedding
        $queryEmbedding = $this->aiService->embed($queryText);

        // Get all experience embeddings with their experiences
        $embeddings = ExperienceEmbedding::with('experience.material', 'experience.jobType', 'experience.parameters')
            ->whereHas('experience', function ($q) {
                $q->where('status', 'approved');
            })
            ->get();

        if ($embeddings->isEmpty()) {
            Log::info('Retriever: No embeddings found in database');
            return new Collection();
        }

        // Calculate similarities
        $results = [];
        foreach ($embeddings as $embedding) {
            $experience = $embedding->experience;
            if (!$experience) {
                continue;
            }

            // Apply filters if context provided
            if ($context) {
                if ($this->filterByMaterial && $context->material) {
                    if (strtolower($experience->material->name) !== strtolower($context->material)) {
                        continue;
                    }
                }
                if ($this->filterByJobType && $context->jobType) {
                    if (strtolower($experience->jobType->name) !== strtolower($context->jobType)) {
                        continue;
                    }
                }
            }

            $similarity = $this->similarityCalculator->cosineSimilarity(
                $queryEmbedding,
                $embedding->embedding
            );

            if ($similarity >= $this->relevanceThreshold) {
                $results[] = [
                    'experience' => $experience,
                    'score' => $similarity,
                    'embedding_id' => $embedding->id,
                ];
            }
        }

        // Sort by score descending
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // Take top K
        $results = array_slice($results, 0, $this->topK);

        // Ensure minimum results
        if (count($results) < $this->minResults && !empty($results)) {
            Log::info('Retriever: Fewer results than min_results', [
                'found' => count($results),
                'min' => $this->minResults,
            ]);
        }

        return new Collection($results);
    }

    public function buildQueryText(string $userMessage, ?QueryContext $context = null): string
    {
        $parts = [];

        if ($context) {
            if ($context->material) {
                $parts[] = "Material: {$context->material}";
            }
            if ($context->thicknessMm) {
                $parts[] = "Espesor: {$context->thicknessMm} mm";
            }
            if ($context->machinePowerW) {
                $parts[] = "Potencia láser: {$context->machinePowerW} W";
            }
            if ($context->jobType) {
                $parts[] = "Tipo de trabajo: {$context->jobType}";
            }
            if ($context->machine) {
                $parts[] = "Máquina: {$context->machine}";
            }
            if ($context->software) {
                $parts[] = "Software: {$context->software}";
            }
        }

        $parts[] = "Consulta del usuario: {$userMessage}";

        return implode(' | ', $parts);
    }
}