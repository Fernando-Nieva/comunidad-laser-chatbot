<?php

namespace App\RAG;

use App\Chat\QueryContext;
use Illuminate\Support\Collection;

class SufficiencyEvaluator
{
    private int $minResults;
    private float $relevanceThreshold;
    private bool $requireMaterialMatch;

    public function __construct(
        int $minResults = 2,
        float $relevanceThreshold = 0.75,
        bool $requireMaterialMatch = true,
    ) {
        $this->minResults = $minResults;
        $this->relevanceThreshold = $relevanceThreshold;
        $this->requireMaterialMatch = $requireMaterialMatch;
    }

    public static function fromConfig(): self
    {
        return new self(
            minResults: config('rag.min_results', 2),
            relevanceThreshold: config('rag.relevance_threshold', 0.75),
            requireMaterialMatch: config('rag.require_material_match', true),
        );
    }

    public function evaluate(Collection $results, ?QueryContext $context = null): SufficiencyResult
    {
        $aboveThreshold = $results->filter(fn($r) => $r['score'] >= $this->relevanceThreshold);

        $reasons = [];
        $usable = new Collection();

        // Check minimum results
        if ($aboveThreshold->count() < $this->minResults) {
            $reasons[] = "Solo {$aboveThreshold->count()} resultado(s) sobre el umbral {$this->relevanceThreshold} (mínimo: {$this->minResults})";
            return new SufficiencyResult(
                sufficient: false,
                reason: implode('; ', $reasons),
                usable: $usable,
            );
        }

        // Check material match if required and context has material
        if ($this->requireMaterialMatch && $context && $context->material) {
            $materialMatches = $aboveThreshold->filter(function ($r) use ($context) {
                return strtolower($r['experience']->material->name) === strtolower($context->material);
            });

            if ($materialMatches->isEmpty()) {
                $reasons[] = "Ningún resultado coincide con el material '{$context->material}'";
                return new SufficiencyResult(
                    sufficient: false,
                    reason: implode('; ', $reasons),
                    usable: $usable,
                );
            }

            $usable = $materialMatches->values();
        } else {
            $usable = $aboveThreshold->values();
        }

        $reasons[] = "{$usable->count()} resultado(s) válido(s) con score >= {$this->relevanceThreshold}";

        return new SufficiencyResult(
            sufficient: true,
            reason: implode('; ', $reasons),
            usable: $usable,
        );
    }
}

class SufficiencyResult
{
    public function __construct(
        public readonly bool $sufficient,
        public readonly string $reason,
        public readonly Collection $usable,
    ) {}

    public function toArray(): array
    {
        return [
            'sufficient' => $this->sufficient,
            'reason' => $this->reason,
            'usable_count' => $this->usable->count(),
        ];
    }
}