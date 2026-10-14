<?php

return [
    'provider' => env('AI_PROVIDER', 'ollama'),

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
        'chat_model' => env('OLLAMA_CHAT_MODEL', 'qwen2.5:7b'),
        'embed_model' => env('OLLAMA_EMBED_MODEL', 'bge-m3'),
        'timeout' => env('OLLAMA_TIMEOUT', 60),
    ],

    'external' => [
        'base_url' => env('EXTERNAL_AI_BASE_URL'),
        'api_key' => env('EXTERNAL_AI_API_KEY'),
        'model' => env('EXTERNAL_AI_MODEL'),
        'timeout' => env('EXTERNAL_AI_TIMEOUT', 30),
    ],

    'search' => [
        'base_url' => env('SEARCH_BASE_URL'),
        'api_key' => env('SEARCH_API_KEY'),
        'timeout' => (int) env('SEARCH_TIMEOUT', 10),
        'max_results' => (int) env('SEARCH_MAX_RESULTS', 5),
    ],

    'chat_history_limit' => (int) env('CHAT_HISTORY_LIMIT', 12),

    // Connectivity checker
    'connectivity_check_url' => env('AI_CONNECTIVITY_CHECK_URL', 'http://www.google.com'),
    'connectivity_check_timeout' => (int) env('AI_CONNECTIVITY_CHECK_TIMEOUT', 2),
    'connectivity_cache_ttl' => (int) env('AI_CONNECTIVITY_CACHE_TTL', 30),
];