<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// Test the multi-turn conversation
Http::fake([
    'http://localhost:11434/api/tags' => Http::response(['models' => []], 200),
    'http://localhost:11434/api/embeddings' => Http::response(['embedding' => array_fill(0, 1024, 0.1)], 200),
    'http://localhost:11434/api/chat' => Http::sequence()
        ->push(['message' => ['content' => 'OK, tenés un Sculpfun S9 de 10W.']], 200)
        ->push(['message' => ['content' => 'Para MDF 3mm con ese láser, prueba 3000 mm/min al 25%.']], 200)
        ->push(['message' => ['content' => 'Para acrílico 5mm, usa 800 mm/min al 70%.']], 200),
]);

$response = \Illuminate\Support\Facades\Http::post('http://localhost:11434/api/chat', [
    'json' => ['message' => 'Tengo un Sculpfun S9 de 10W'],
]);

echo "Response 1: " . $response->json('message')['content'] . "\n";

$response2 = \Illuminate\Support\Facades\Http::post('http://localhost:11434/api/chat', [
    'json' => ['message' => '¿Velocidad para MDF 3mm?'],
]);

echo "Response 2: " . $response2->json('message')['content'] . "\n";

$response3 = \Illuminate\Support\Facades\Http::post('http://localhost:11434/api/chat', [
    'json' => ['message' => '¿Y para acrílico 5mm?'],
]);

echo "Response 3: " . $response3->json('message')['content'] . "\n";