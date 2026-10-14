<?php

namespace App\Console\Commands;

use App\Chat\QueryContext;
use App\RAG\Retriever;
use App\RAG\SufficiencyEvaluator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RagCalibrateCommand extends Command
{
    protected $signature = 'rag:calibrate {--step=0.05 : Paso entre umbrales} {--min=0.50 : Umbral mínimo} {--max=0.90 : Umbral máximo}';
    protected $description = 'Calibra el umbral de relevancia RAG probando contra consultas etiquetadas';

    public function handle(Retriever $retriever): int
    {
        $step = (float) $this->option('step');
        $min = (float) $this->option('min');
        $max = (float) $this->option('max');

        $calibrationData = json_decode(file_get_contents(database_path('rag_calibration.json')), true);

        if (!$calibrationData) {
            $this->error('No se pudo cargar database/rag_calibration.json');
            return self::FAILURE;
        }

        $this->info("Calibrando con " . (isset($calibrationData['count']) ? $calibrationData['count'] : count($calibrationData)) . " consultas...");
        $this->info("Rango: {$min} a {$max}, paso: {$step}");

        // First, evaluate each query at current threshold
        $this->evaluateCurrentThreshold($retriever, $calibrationData);

        // Then sweep thresholds
        $bestThreshold = $this->sweepThresholds($retriever, $calibrationData, $min, $max, $step);

        $this->newLine();
        $this->info("Umbral recomendado: {$bestThreshold}");
        $this->info("Actualiza .env: RAG_RELEVANCE_THRESHOLD={$bestThreshold}");

        return self::SUCCESS;
    }

    private function evaluateCurrentThreshold(Retriever $retriever, array $queries): void
    {
        $evaluator = SufficiencyEvaluator::fromConfig();

        $this->newLine();
        $this->info('--- Evaluación en umbral actual ---');

        $headers = ['#', 'Consulta', 'Esperado', 'Score Máx', 'Decisión', 'Razón'];
        $rows = [];

        foreach ($queries as $index => $query) {
            $context = $this->buildContext($query['context'] ?? []);
            $results = $retriever->retrieve($query['query'], $context);
            $evaluation = $evaluator->evaluate($results, $context);

            $maxScore = $results->isEmpty() ? 0 : $results->max('score');
            $decision = $evaluation->sufficient ? 'SUFICIENTE' : 'INSUFICIENTE';
            $expected = $query['expected'] === 'sufficient' ? 'SUFICIENTE' : 'INSUFICIENTE';
            $match = $decision === $expected ? '✓' : '✗';

            $rows[] = [
                $index + 1,
                mb_strimwidth($query['query'], 0, 40, '...'),
                $expected,
                number_format($maxScore, 4),
                $decision . ' ' . $match,
                mb_strimwidth($evaluation->reason, 0, 50, '...'),
            ];
        }

        $this->table($headers, $rows);
    }

    private function sweepThresholds(Retriever $retriever, array $queries, float $min, float $max, float $step): float
    {
        $this->newLine();
        $this->info('--- Barrido de umbrales ---');

        $bestThreshold = $min;
        $bestScore = -1;

        $thresholds = [];
        for ($t = $min; $t <= $max + 0.0001; $t += $step) {
            $thresholds[] = round($t, 2);
        }

        $headers = ['Umbral', 'Aciertos', 'Falsos +', 'Falsos -', 'Precisión', 'Recall', 'F1'];
        $rows = [];

        foreach ($thresholds as $threshold) {
            $stats = $this->evaluateAtThreshold($retriever, $queries, $threshold);

            $precision = $stats['tp'] + $stats['fp'] > 0 ? $stats['tp'] / ($stats['tp'] + $stats['fp']) : 0;
            $recall = $stats['tp'] + $stats['fn'] > 0 ? $stats['tp'] / ($stats['tp'] + $stats['fn']) : 0;
            $f1 = $precision + $recall > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0;

            $rows[] = [
                $threshold,
                $stats['tp'],
                $stats['fp'],
                $stats['fn'],
                number_format($precision, 3),
                number_format($recall, 3),
                number_format($f1, 3),
            ];

            if ($f1 > $bestScore) {
                $bestScore = $f1;
                $bestThreshold = $threshold;
            }
        }

        $this->table($headers, $rows);

        return $bestThreshold;
    }

    private function evaluateAtThreshold(Retriever $retriever, array $queries, float $threshold): array
    {
        $tp = $fp = $fn = $tn = 0;

        $evaluator = new SufficiencyEvaluator(
            minResults: config('rag.min_results', 2),
            relevanceThreshold: $threshold,
            requireMaterialMatch: config('rag.require_material_match', true),
        );

        foreach ($queries as $query) {
            $context = $this->buildContext($query['context'] ?? []);
            $results = $retriever->retrieve($query['query'], $context);
            $evaluation = $evaluator->evaluate($results, $context);

            $predicted = $evaluation->sufficient;
            $expected = $query['expected'] === 'sufficient';

            if ($predicted && $expected) {
                $tp++;
            } elseif ($predicted && !$expected) {
                $fp++;
            } elseif (!$predicted && $expected) {
                $fn++;
            } else {
                $tn++;
            }
        }

        return compact('tp', 'fp', 'fn', 'tn');
    }

    private function buildContext(array $contextData): ?QueryContext
    {
        if (empty($contextData)) {
            return null;
        }

        return new QueryContext(
            material: $contextData['material'] ?? null,
            thicknessMm: isset($contextData['thickness_mm']) ? (float) $contextData['thickness_mm'] : null,
            machinePowerW: isset($contextData['machine_power_w']) ? (int) $contextData['machine_power_w'] : null,
            jobType: $contextData['job_type'] ?? null,
            machine: $contextData['machine'] ?? null,
            software: $contextData['software'] ?? null,
        );
    }
}