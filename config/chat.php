<?php

return [
    'max_message_length' => (int) env('CHAT_MAX_MESSAGE_LENGTH', 1000),

    'rate_limit' => [
        'max_attempts' => (int) env('CHAT_RATE_LIMIT_MAX_ATTEMPTS', 30),
        'decay_minutes' => (int) env('CHAT_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    // UI disclaimer always shown
    'ui_disclaimer' => 'Las respuestas son orientativas y no garantizan un resultado. Probá siempre en un retazo antes del trabajo final.',
];