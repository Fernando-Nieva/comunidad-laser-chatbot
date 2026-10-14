<?php

namespace App\Console\Commands;

use App\Chat\QueryContext;
use App\RAG\Retriever;
use Illuminate\Console\Command;

class RagSearchCommand extends Command
{
    protected $signature = 'rag:search {query} {--material=} {--job-type=} {--thickness=} {--power=}';
    protected $description = 'Busca experiencias similares usando RAG (para depuración)';

    public function handle(Retriever $retriever): int
    {
        $query = $this->argument('query');

        $context = null;
        if ($this->option('material') || $this->option('job-type') || $this->option('thickness') || $this->option('power')) {
            $context = new QueryContext(
                material: $this->option('material') ?: null,
                thicknessMm: $this->option('thickness') ? (float) $this->option('thickness') : null,
                machinePowerW: $this->option('power') ? (int) $this->option('power') : null,
                jobType: $this->option('job-type') ?: null,
            );
        }

        $this->info("Buscando: {$query}");
        if ($context && !$context->isEmpty()) {
            $this->info('Contexto: ' . json_encode($context->toArray()));
        }

        $results = $retriever->retrieve($query, $context);

        if ($results->isEmpty()) {
            $this->warn('No se encontraron resultados.');
            return self::SUCCESS;
        }

        $headers = ['Score', 'Título', 'Material', 'Espesor (mm)', 'Potencia (W)', 'Máquina', 'Software'];
        $rows = [];

        foreach ($results as $result) {
            $exp = $result['experience'];
            $rows[] = [
                number_format($result['score'], 4),
                $exp->title,
                $exp->material->name,
                $exp->thickness_mm ?? 'N/A',
                $exp->machine_power_w ?? 'N/A',
                $exp->machine,
                $exp->software,
            ];
        }

        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}