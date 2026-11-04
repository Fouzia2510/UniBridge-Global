<?php

return [
    'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
    'model' => env('OLLAMA_MODEL', 'gemma3:1b'),
    'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
];