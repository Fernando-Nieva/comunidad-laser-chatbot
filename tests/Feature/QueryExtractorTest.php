<?php

use App\Chat\ChatService;
use App\Chat\MissingInfoPolicy;
use App\Chat\QueryContext;
use App\Chat\QueryExtractor;
use Illuminate\Support\Facades\Http;

test('QueryContext can be created from array', function () {
    $context = QueryContext::fromArray([
        'material' => 'MDF',
        'thickness_mm' => 3.0,
        'machine_power_w' => 10,
        'job_type' => 'grabado',
        'machine' => 'Sculpfun S9',
        'software' => 'LightBurn',
    ]);

    expect($context->material)->toBe('MDF');
    expect($context->thicknessMm)->toBe(3.0);
    expect($context->machinePowerW)->toBe(10);
    expect($context->jobType)->toBe('grabado');
    expect($context->machine)->toBe('Sculpfun S9');
    expect($context->software)->toBe('LightBurn');
});

test('QueryContext handles null values', function () {
    $context = QueryContext::fromArray([
        'material' => 'MDF',
        'thickness_mm' => null,
        'machine_power_w' => null,
        'job_type' => null,
        'machine' => null,
        'software' => null,
    ]);

    expect($context->material)->toBe('MDF');
    expect($context->thicknessMm)->toBeNull();
    expect($context->machinePowerW)->toBeNull();
    expect($context->jobType)->toBeNull();
    expect($context->machine)->toBeNull();
    expect($context->software)->toBeNull();
});

test('QueryContext merges with other context preferring non-null values', function () {
    $ctx1 = new QueryContext(material: 'MDF', thicknessMm: 3.0);
    $ctx2 = new QueryContext(material: 'Acrílico', machinePowerW: 20);

    $merged = $ctx1->merge($ctx2);

    expect($merged->material)->toBe('Acrílico');
    expect($merged->thicknessMm)->toBe(3.0);
    expect($merged->machinePowerW)->toBe(20);
});

test('QueryContext isEmpty returns true for all null', function () {
    $context = new QueryContext();
    expect($context->isEmpty())->toBeTrue();
});

test('QueryContext isEmpty returns false when any field set', function () {
    $context = new QueryContext(material: 'MDF');
    expect($context->isEmpty())->toBeFalse();
});

test('QueryContext toArray returns all fields', function () {
    $context = new QueryContext(material: 'MDF', thicknessMm: 3.0);
    $array = $context->toArray();

    expect($array)->toHaveKeys([
        'material', 'thickness_mm', 'machine_power_w',
        'job_type', 'machine', 'software',
    ]);
});

test('MissingInfoPolicy getMissingRequired returns missing required fields', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
    );

    $context = new QueryContext(material: 'MDF');
    $missing = $policy->getMissingRequired($context);

    expect($missing)->toBe(['job_type']);
});

test('MissingInfoPolicy getMissingRecommended returns missing recommended fields', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
    );

    $context = new QueryContext(material: 'MDF', jobType: 'grabado');
    $missing = $policy->getMissingRecommended($context);

    expect($missing)->toBe(['thickness_mm', 'machine_power_w']);
});

test('MissingInfoPolicy getNextQuestion returns first missing required', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
    );

    $context = new QueryContext();
    $question = $policy->getNextQuestion($context);

    expect($question)->toBe('¿Qué material vas a grabar/cortar?');
});

test('MissingInfoPolicy getNextQuestion returns first missing recommended after required complete', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
    );

    $context = new QueryContext(material: 'MDF', jobType: 'grabado');
    $question = $policy->getNextQuestion($context);

    expect($question)->toBe('¿Qué espesor tiene el material (en mm)?');
});

test('MissingInfoPolicy getNextQuestion returns null when max questions reached', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
        maxQuestionsInRow: 2,
    );

    $context = new QueryContext(material: 'MDF', jobType: 'grabado');
    $question = $policy->getNextQuestion($context, questionsAsked: 2);

    expect($question)->toBeNull();
});

test('MissingInfoPolicy isReadyToSearch returns false when required missing', function () {
    $policy = new MissingInfoPolicy(required: ['material', 'job_type']);
    $context = new QueryContext(material: 'MDF');

    expect($policy->isReadyToSearch($context))->toBeFalse();
});

test('MissingInfoPolicy isReadyToSearch returns true when required complete', function () {
    $policy = new MissingInfoPolicy(required: ['material', 'job_type']);
    $context = new QueryContext(material: 'MDF', jobType: 'grabado');

    expect($policy->isReadyToSearch($context))->toBeTrue();
});

test('MissingInfoPolicy isReadyToSearch returns true when max questions reached even if recommended missing', function () {
    $policy = new MissingInfoPolicy(
        required: ['material', 'job_type'],
        recommended: ['thickness_mm', 'machine_power_w'],
        maxQuestionsInRow: 2,
    );
    $context = new QueryContext(material: 'MDF', jobType: 'grabado');

    expect($policy->isReadyToSearch($context, questionsAsked: 2))->toBeTrue();
});

test('MissingInfoPolicy formatQuestion returns correct questions', function () {
    $policy = new MissingInfoPolicy();

    expect($policy->formatQuestion('material'))->toBe('¿Qué material vas a grabar/cortar?');
    expect($policy->formatQuestion('job_type'))->toBe('¿Qué tipo de trabajo? (grabado, corte, marcado, grabado y corte)');
    expect($policy->formatQuestion('thickness_mm'))->toBe('¿Qué espesor tiene el material (en mm)?');
    expect($policy->formatQuestion('machine_power_w'))->toBe('¿Cuántos vatios tiene tu láser?');
    expect($policy->formatQuestion('machine'))->toBe('¿Qué modelo de máquina usás?');
    expect($policy->formatQuestion('software'))->toBe('¿Qué software usás? (LightBurn, LaserGRBL, etc.)');
});

test('MissingInfoPolicy isParameterQuery detects parameter queries', function () {
    $policy = new MissingInfoPolicy();

    expect($policy->isParameterQuery('¿Qué velocidad para MDF 3mm?'))->toBeTrue();
    expect($policy->isParameterQuery('Parámetros para grabar acrílico'))->toBeTrue();
    expect($policy->isParameterQuery('Potencia y pasadas para corte'))->toBeTrue();
    expect($policy->isParameterQuery('Configuración recomendada'))->toBeTrue();
    expect($policy->isParameterQuery('Mejores ajustes para MDF'))->toBeTrue();
});

test('MissingInfoPolicy isParameterQuery rejects conceptual queries', function () {
    $policy = new MissingInfoPolicy();

    expect($policy->isParameterQuery('¿Qué es el DPI?'))->toBeFalse();
    expect($policy->isParameterQuery('¿Qué diferencia hay entre láser diodo y CO2?'))->toBeFalse();
    expect($policy->isParameterQuery('Explícame cómo funciona el láser'))->toBeFalse();
    expect($policy->isParameterQuery('Para qué sirve el LPI'))->toBeFalse();
    expect($policy->isParameterQuery('Definición de hatch'))->toBeFalse();
});

test('QueryExtractor extracts context from conversation', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
        'http://localhost:11434/api/chat' => Http::response([
            'message' => ['content' => '{"material": "MDF", "thickness_mm": 3, "machine_power_w": 10, "job_type": "grabado", "machine": null, "software": "LightBurn"}'],
        ], 200),
    ]);

    $extractor = app(QueryExtractor::class);
    $context = $extractor->extract([
        ['role' => 'user', 'content' => 'Tengo MDF 3mm con láser 10W'],
        ['role' => 'assistant', 'content' => 'Entendido'],
        ['role' => 'user', 'content' => 'Uso LightBurn'],
    ]);

    expect($context->material)->toBe('MDF');
    expect($context->thicknessMm)->toBe(3.0);
    expect($context->machinePowerW)->toBe(10);
    expect($context->jobType)->toBe('grabado');
    expect($context->software)->toBe('LightBurn');
});

test('QueryExtractor handles JSON in markdown code blocks', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
        'http://localhost:11434/api/chat' => Http::response([
            'message' => ['content' => '```json\n{"material": "Acrílico", "thickness_mm": 5, "machine_power_w": 20, "job_type": "corte", "machine": "Sculpfun S9", "software": null}\n```'],
        ], 200),
    ]);

    $extractor = app(QueryExtractor::class);
    $context = $extractor->extract([
        ['role' => 'user', 'content' => 'Corte acrílico 5mm'],
    ]);

    expect($context->material)->toBe('Acrílico');
    expect($context->thicknessMm)->toBe(5.0);
    expect($context->machinePowerW)->toBe(20);
    expect($context->jobType)->toBe('corte');
});

test('QueryExtractor retries on invalid JSON then returns empty context', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
        'http://localhost:11434/api/chat' => Http::sequence()
            ->push(['message' => ['content' => 'Esto no es JSON válido']], 200)
            ->push(['message' => ['content' => 'Tampoco esto es JSON']], 200),
    ]);

    $extractor = app(QueryExtractor::class);
    $context = $extractor->extract([
        ['role' => 'user', 'content' => 'Test'],
    ]);

    expect($context->isEmpty())->toBeTrue();
});

test('QueryExtractor accumulates context across conversation turns', function () {
    Http::fake([
        'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
        'http://localhost:11434/api/chat' => Http::sequence()
            ->push(['message' => ['content' => '{"material": "MDF", "thickness_mm": null, "machine_power_w": null, "job_type": null, "machine": null, "software": null}'], 200])
            ->push(['message' => ['content' => '{"material": "MDF", "thickness_mm": 3, "machine_power_w": null, "job_type": "grabado", "machine": null, "software": null}'], 200])
            ->push(['message' => ['content' => '{"material": "MDF", "thickness_mm": 3, "machine_power_w": 10, "job_type": "grabado", "machine": "Sculpfun S9", "software": "LightBurn"}'], 200]),
    ]);

    $extractor = app(QueryExtractor::class);

    // Turn 1
    $ctx1 = $extractor->extract([
        ['role' => 'user', 'content' => 'Tengo MDF'],
    ]);
    expect($ctx1->material)->toBe('MDF');

    // Turn 2
    $ctx2 = $extractor->extract([
        ['role' => 'user', 'content' => 'Tengo MDF'],
        ['role' => 'assistant', 'content' => 'OK'],
        ['role' => 'user', 'content' => 'Espesor 3mm, grabado'],
    ]);
    expect($ctx2->material)->toBe('MDF');
    expect($ctx2->thicknessMm)->toBe(3.0);
    expect($ctx2->jobType)->toBe('grabado');

    // Turn 3
    $ctx3 = $extractor->extract([
        ['role' => 'user', 'content' => 'Tengo MDF'],
        ['role' => 'assistant', 'content' => 'OK'],
        ['role' => 'user', 'content' => 'Espesor 3mm, grabado'],
        ['role' => 'assistant', 'content' => 'OK'],
        ['role' => 'user', 'content' => 'Láser 10W, Sculpfun S9, LightBurn'],
    ]);
    expect($ctx3->material)->toBe('MDF');
    expect($ctx3->thicknessMm)->toBe(3.0);
    expect($ctx3->machinePowerW)->toBe(10);
    expect($ctx3->jobType)->toBe('grabado');
    expect($ctx3->machine)->toBe('Sculpfun S9');
    expect($ctx3->software)->toBe('LightBurn');
});