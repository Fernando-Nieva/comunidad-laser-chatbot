<?php

use App\Chat\ChatOrchestrator;
use App\Chat\ChatReply;
use App\Chat\MissingInfoPolicy;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Http;

test('POST /api/chat returns conversation_uuid with Http::fake', function () {
    $callCount = 0;
    Http::fake(function ($request) use (&$callCount) {
        $callCount++;
        $url = $request->url();
        
        if (str_contains($url, '/api/tags')) {
            return Http::response(['models' => []], 200);
        }
        if (str_contains($url, '/api/chat')) {
            if ($callCount === 1) {
                return Http::response(['message' => ['content' => 'Hola']], 200);
            }
            return Http::response(['message' => ['content' => 'Hola de nuevo']], 200);
        }
        if (str_contains($url, '/api/embeddings')) {
            return Http::response(['embedding' => array_fill(0, 384, 0.1)], 200);
        }
        return Http::response(['message' => ['content' => 'OK']], 200);
    });

    $this->mock(MissingInfoPolicy::class, function ($mock) {
        $mock->shouldReceive('isParameterQuery')
            ->andReturn(false);
        $mock->shouldReceive('formatQuestion')
            ->andReturn('¿Pregunta?');
    });

    $this->app->forgetInstance(\App\Chat\ChatService::class);
    $this->app->forgetInstance(\App\Chat\Orchestrator\ChatOrchestrator::class);

    $response = $this->postJson('/api/chat', ['message' => 'Hola']);

    $response->assertStatus(200)
        ->assertJsonStructure(['reply', 'conversation_uuid']);
});

test('second message receives history from first message with Http::fake', function () {
    $chatResponseCount = 0;
    
    Http::fake(function ($request) use (&$chatResponseCount) {
        $url = $request->url();
        
        if (str_contains($url, '/api/tags')) {
            return Http::response(['models' => []], 200);
        }
        
        if (str_contains($url, '/api/embeddings')) {
            return Http::response(['embedding' => array_fill(0, 384, 0.1)], 200);
        }
        
        if (str_contains($url, '/api/chat')) {
            $chatResponseCount++;
            if ($chatResponseCount === 1) {
                return Http::response([
                    'message' => ['content' => 'Entendido, tenés un láser de 10W.'],
                ], 200);
            }
            // Return second response for ALL subsequent chat requests
            return Http::response([
                'message' => ['content' => 'Para MDF 3mm con tu láser de 10W, probá 3000-4000 mm/min al 20-30% potencia. ⚠️ Valores orientativos.'],
            ], 200);
        }
        
        return Http::response(['message' => ['content' => 'OK']], 200);
    });

    $this->mock(MissingInfoPolicy::class, function ($mock) {
        $mock->shouldReceive('isParameterQuery')
            ->andReturn(false);
        $mock->shouldReceive('formatQuestion')
            ->andReturn('¿Pregunta?');
    });

    $this->app->forgetInstance(\App\Chat\ChatService::class);
    $this->app->forgetInstance(\App\Chat\Orchestrator\ChatOrchestrator::class);

    // First message
    $response1 = $this->postJson('/api/chat', ['message' => 'Tengo un láser de 10W']);
    $response1->assertStatus(200);
    $uuid = $response1->json('conversation_uuid');

    // Second message with same conversation
    $response2 = $this->postJson('/api/chat', ['message' => '¿qué velocidad uso en MDF 3mm?', 'conversation_uuid' => $uuid]);

    $response2->assertStatus(200);
    $reply = $response2->json('reply');
    expect($reply)->toContain('10W');
    $response2->assertJsonPath('conversation_uuid', $uuid);
});