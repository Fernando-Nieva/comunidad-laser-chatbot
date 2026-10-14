<?php

use App\Chat\QueryContext;
use App\RAG\SufficiencyEvaluator;
use Illuminate\Support\Collection;

test('SufficiencyEvaluator evaluate returns a result object with expected properties', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.5,
    );

    $results = new Collection([
        ['score' => 0.8, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation)->toHaveProperties(['sufficient', 'reason', 'usable']);
    expect($evaluation->toArray())->toHaveKeys(['sufficient', 'reason', 'usable_count']);
});

test('evaluate returns insufficient when no results', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 2,
        relevanceThreshold: 0.75,
    );

    $results = new Collection();
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeFalse();
    expect($evaluation->reason)->toContain('Solo 0 resultado(s)');
});

test('evaluate returns insufficient when below minResults', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 2,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.8, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeFalse();
    expect($evaluation->reason)->toContain('Solo 1 resultado(s)');
});

test('evaluate returns sufficient when minResults met', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 2,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.8, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(2);
});

test('evaluate filters by threshold', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.8, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
        ['score' => 0.5, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]], // Below threshold
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(1); // Only the one above threshold
});

test('evaluate requires material match when context has material', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.5,
        requireMaterialMatch: true,
    );

    $results = new Collection([
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'Acrílico']]], // Different material
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeFalse();
    expect($evaluation->reason)->toContain("material 'MDF'");
});

test('evaluate ignores material match when context has no material', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.5,
        requireMaterialMatch: true,
    );

    $results = new Collection([
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'Acrílico']]],
    ]);
    $context = new QueryContext(); // No material

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(1);
});

test('evaluate ignores material match when requireMaterialMatch is false', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.5,
        requireMaterialMatch: false,
    );

    $results = new Collection([
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'Acrílico']]],
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(1);
});

test('evaluate at exact threshold includes result', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.75, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]], // Exactly at threshold
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(1);
});

test('evaluate just below threshold excludes result', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 1,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.749, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]], // Just below
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeFalse();
});

test('evaluate returns only usable results above threshold', function () {
    $evaluator = new SufficiencyEvaluator(
        minResults: 2,
        relevanceThreshold: 0.75,
    );

    $results = new Collection([
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
        ['score' => 0.8, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
        ['score' => 0.6, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]], // Below
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue();
    expect($evaluation->usable->count())->toBe(2);
    expect($evaluation->usable->every(fn($r) => $r['score'] >= 0.75))->toBeTrue();
});

test('fromConfig creates evaluator with config values', function () {
    config([
        'rag.min_results' => 3,
        'rag.relevance_threshold' => 0.8,
        'rag.require_material_match' => false,
    ]);

    $evaluator = SufficiencyEvaluator::fromConfig();

    $results = new Collection([
        ['score' => 0.85, 'experience' => (object) ['material' => (object) ['name' => 'Acrílico']]],
        ['score' => 0.82, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
        ['score' => 0.9, 'experience' => (object) ['material' => (object) ['name' => 'MDF']]],
    ]);
    $context = new QueryContext(material: 'MDF');

    $evaluation = $evaluator->evaluate($results, $context);

    expect($evaluation->sufficient)->toBeTrue(); // requireMaterialMatch is false
    expect($evaluation->usable->count())->toBe(3);
});