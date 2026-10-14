<?php

use App\Chat\PostProcessor;

test('PostProcessor adds fixed disclaimer to reply', function () {
    $processor = new PostProcessor();
    $reply = 'Para MDF 3mm usa 3000 mm/min.';

    $processed = $processor->process($reply, 'community', [1], [0.9]);

    expect($processed)->toContain('Las respuestas son orientativas y no garantizan un resultado');
    expect($processed)->toContain('Probá siempre en un retazo antes del trabajo final');
});

test('PostProcessor does not duplicate fixed disclaimer', function () {
    $processor = new PostProcessor();
    $reply = 'Respuesta con disclaimer.\n\n---\n**⚠️ Las respuestas son orientativas y no garantizan un resultado. Probá siempre en un retazo antes del trabajo final.**';

    $processed = $processor->process($reply, 'community');

    // Should only appear once
    expect(substr_count($processed, 'Las respuestas son orientativas'))->toBe(1);
});

test('PostProcessor adds community source label', function () {
    $processor = new PostProcessor();
    $reply = 'Respuesta de prueba.';

    $processed = $processor->process($reply, 'community', [1, 2], [0.9, 0.8]);

    expect($processed)->toContain('Basado en experiencias de la comunidad');
    expect($processed)->toContain('IDs: 1, 2');
});

test('PostProcessor adds external_ai source label', function () {
    $processor = new PostProcessor();
    $reply = 'Respuesta externa.';

    $processed = $processor->process($reply, 'external_ai', [], []);

    expect($processed)->toContain('Orientación de IA externa');
});

test('PostProcessor adds local_ai source label', function () {
    $processor = new PostProcessor();
    $reply = 'Respuesta local.';

    $processed = $processor->process($reply, 'local_ai', [], []);

    expect($processed)->toContain('Orientación de IA local');
});

test('PostProcessor adds non-community disclaimer for external_ai', function () {
    $processor = new PostProcessor();
    $reply = 'Velocidad 3000 mm/min.';

    $processed = $processor->process($reply, 'external_ai', [], []);

    expect($processed)->toContain('NO de experiencias de la comunidad');
    expect($processed)->toContain('orientación general de IA');
});

test('PostProcessor adds non-community disclaimer for local_ai', function () {
    $processor = new PostProcessor();
    $reply = 'Potencia 30%.';

    $processed = $processor->process($reply, 'local_ai', [], []);

    expect($processed)->toContain('NO de experiencias de la comunidad');
});

test('PostProcessor does not add non-community disclaimer for community source', function () {
    $processor = new PostProcessor();
    $reply = 'Respuesta comunitaria.';

    $processed = $processor->process($reply, 'community', [1], [0.9]);

    expect($processed)->not->toContain('NO de experiencias de la comunidad');
});

test('PostProcessor adds PVC safety warning', function () {
    $processor = new PostProcessor();
    $reply = 'Para cortar PVC usa velocidad 500.';

    $processed = $processor->process($reply, 'community', [], []);

    expect($processed)->toContain('PVC');
    expect($processed)->toContain('ácido clorhídrico');
    expect($processed)->toContain('ventilación forzada');
});

test('PostProcessor adds multiple safety warnings', function () {
    $processor = new PostProcessor();
    $reply = 'PVC y vinilo son materiales peligrosos.';

    $processed = $processor->process($reply, 'community', [], []);

    expect($processed)->toContain('PVC');
    expect($processed)->toContain('vinilo');
    expect($processed)->toContain('cloro');
});

test('PostProcessor does not add safety warning for safe materials', function () {
    $processor = new PostProcessor();
    $reply = 'Para MDF usa velocidad 3000.';

    $processed = $processor->process($reply, 'community', [], []);

    expect($processed)->not->toContain('ATENCIÓN');
    expect($processed)->not->toContain('PVC');
});