<?php

use App\Chat\QueryContext;
use App\Models\Experience;
use App\Models\ExperienceEmbedding;
use App\Models\JobType;
use App\Models\Material;
use App\RAG\Retriever;
use Illuminate\Support\Facades\Http;

test('Retriever retrieve returns empty collection when no embeddings', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => 0.75,
        'rag.min_results' => 2,
        'rag.filter_by_material' => true,
        'rag.filter_by_job_type' => true,
    ]);

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn(array_fill(0, 384, 0.1));
    });

    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query');

    expect($results)->toHaveCount(0);
});

test('Retriever retrieve orders by similarity descending', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => -1.0,
        'rag.min_results' => 2,
        'rag.filter_by_material' => false,
        'rag.filter_by_job_type' => false,
    ]);

    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);

    // Create 3 experiences with known embeddings
    $exp1 = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'title' => 'Exp 1 - High similarity',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $exp1->id,
        'embedding' => [1.0, 0.0, 0.0], // Will match query [1,0,0] perfectly
        'model' => 'bge-m3',
    ]);

    $exp2 = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'title' => 'Exp 2 - Medium similarity',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $exp2->id,
        'embedding' => [0.0, 1.0, 0.0], // Orthogonal to query
        'model' => 'bge-m3',
    ]);

    $exp3 = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'title' => 'Exp 3 - Negative similarity',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $exp3->id,
        'embedding' => [-1.0, 0.0, 0.0], // Opposite to query
        'model' => 'bge-m3',
    ]);

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]); // Query matches exp1 perfectly
    });

    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query');

    expect($results)->toHaveCount(3);
    expect($results[0]['experience']->id)->toBe($exp1->id);
    expect($results[0]['score'])->toBe(1.0);
    expect($results[1]['experience']->id)->toBe($exp2->id);
    expect($results[1]['score'])->toBe(0.0);
    expect($results[2]['experience']->id)->toBe($exp3->id);
    expect($results[2]['score'])->toBe(-1.0);
});

test('Retriever retrieve respects top_k limit', function () {
    config([
        'rag.top_k' => 2,
        'rag.relevance_threshold' => 0.0,
        'rag.min_results' => 1,
        'rag.filter_by_material' => false,
        'rag.filter_by_job_type' => false,
    ]);

    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);

    for ($i = 1; $i <= 5; $i++) {
        $exp = Experience::factory()->create([
            'material_id' => $material->id,
            'job_type_id' => $jobType->id,
            'title' => "Exp $i",
        ]);
        ExperienceEmbedding::factory()->create([
            'experience_id' => $exp->id,
            'embedding' => [1.0 / $i, 0.0, 0.0], // Decreasing similarity
            'model' => 'bge-m3',
        ]);
    }

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]);
    });

    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query');

    expect($results)->toHaveCount(2); // top_k = 2
});

test('Retriever retrieve filters by material when context provided', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => 0.0,
        'rag.min_results' => 1,
        'rag.filter_by_material' => true,
        'rag.filter_by_job_type' => false,
    ]);

    $mdf = Material::factory()->create(['name' => 'MDF']);
    $acrilico = Material::factory()->create(['name' => 'Acrílico']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);

    $expMdf = Experience::factory()->create([
        'material_id' => $mdf->id,
        'job_type_id' => $jobType->id,
        'title' => 'MDF Experience',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expMdf->id,
        'embedding' => [0.5, 0.5, 0.0],
        'model' => 'bge-m3',
    ]);

    $expAcrilico = Experience::factory()->create([
        'material_id' => $acrilico->id,
        'job_type_id' => $jobType->id,
        'title' => 'Acrílico Experience',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expAcrilico->id,
        'embedding' => [1.0, 0.0, 0.0], // Higher similarity but different material
        'model' => 'bge-m3',
    ]);

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]); // Would match Acrílico better
    });

    $context = new QueryContext(material: 'MDF');
    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query', $context);

    // Should only return MDF experience despite lower similarity
    expect($results)->toHaveCount(1);
    expect($results[0]['experience']->id)->toBe($expMdf->id);
});

test('Retriever retrieve filters by job_type when context provided', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => 0.0,
        'rag.min_results' => 1,
        'rag.filter_by_material' => false,
        'rag.filter_by_job_type' => true,
    ]);

    $material = Material::factory()->create(['name' => 'MDF']);
    $grabado = JobType::factory()->create(['name' => 'Grabado']);
    $corte = JobType::factory()->create(['name' => 'Corte']);

    $expGrabado = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $grabado->id,
        'title' => 'Grabado Experience',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expGrabado->id,
        'embedding' => [0.5, 0.5, 0.0],
        'model' => 'bge-m3',
    ]);

    $expCorte = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $corte->id,
        'title' => 'Corte Experience',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expCorte->id,
        'embedding' => [1.0, 0.0, 0.0], // Higher similarity but different job type
        'model' => 'bge-m3',
    ]);

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]);
    });

    $context = new QueryContext(jobType: 'Grabado');
    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query', $context);

    expect($results)->toHaveCount(1);
    expect($results[0]['experience']->id)->toBe($expGrabado->id);
});

test('Retriever retrieve respects relevance_threshold', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => 0.8,
        'rag.min_results' => 1,
        'rag.filter_by_material' => false,
        'rag.filter_by_job_type' => false,
    ]);

    $material = Material::factory()->create(['name' => 'MDF']);
    $jobType = JobType::factory()->create(['name' => 'Grabado']);

    $expHigh = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'title' => 'High Sim',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expHigh->id,
        'embedding' => [1.0, 0.0, 0.0],
        'model' => 'bge-m3',
    ]);

    $expLow = Experience::factory()->create([
        'material_id' => $material->id,
        'job_type_id' => $jobType->id,
        'title' => 'Low Sim',
    ]);
    ExperienceEmbedding::factory()->create([
        'experience_id' => $expLow->id,
        'embedding' => [0.5, 0.5, 0.0], // ~0.707 similarity
        'model' => 'bge-m3',
    ]);

    $this->mock(\App\AI\AIService::class, function ($mock) {
        $mock->shouldReceive('embed')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]);
    });

    $retriever = app(Retriever::class);
    $results = $retriever->retrieve('test query');

    expect($results)->toHaveCount(1);
    expect($results[0]['experience']->id)->toBe($expHigh->id);
});

test('Retriever buildQueryText includes context fields', function () {
    config([
        'rag.top_k' => 5,
        'rag.relevance_threshold' => 0.75,
        'rag.min_results' => 2,
    ]);

    $retriever = app(Retriever::class);

    $context = new QueryContext(
        material: 'MDF',
        thicknessMm: 3.0,
        machinePowerW: 10,
        jobType: 'Grabado',
        machine: 'Sculpfun S9',
        software: 'LightBurn'
    );

    $queryText = $retriever->buildQueryText('¿Qué velocidad?', $context);

    expect($queryText)->toContain('Material: MDF');
    expect($queryText)->toContain('Espesor: 3 mm');
    expect($queryText)->toContain('Potencia láser: 10 W');
    expect($queryText)->toContain('Tipo de trabajo: Grabado');
    expect($queryText)->toContain('Máquina: Sculpfun S9');
    expect($queryText)->toContain('Software: LightBurn');
    expect($queryText)->toContain('Consulta del usuario: ¿Qué velocidad?');
});

test('Retriever buildQueryText works with partial context', function () {
    $retriever = app(Retriever::class);

    $context = new QueryContext(material: 'Acrílico');
    $queryText = $retriever->buildQueryText('corte', $context);

    expect($queryText)->toContain('Material: Acrílico');
    expect($queryText)->not->toContain('Espesor:');
    expect($queryText)->not->toContain('Potencia láser:');
});

test('Retriever buildQueryText works with null context', function () {
    $retriever = app(Retriever::class);

    $queryText = $retriever->buildQueryText('hola', null);

    expect($queryText)->toBe('Consulta del usuario: hola');
});