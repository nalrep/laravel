<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "openai", "ollama"
    |
    */
    'driver' => env('NARLREP_DRIVER', 'openai'),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('NARLREP_OPENAI_MODEL', 'gpt-4-turbo'),
    ],

    'ollama' => [
        'url' => env('OLLAMA_URL', 'http://localhost:11434'),
        'model' => env('NARLREP_OLLAMA_MODEL', 'llama3'),
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('NARLREP_OPENROUTER_MODEL', 'openai/gpt-3.5-turbo'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety Settings
    |--------------------------------------------------------------------------
    |
    */
    'safety' => [
        'allow_destructive' => false, // If true, allows DELETE/UPDATE (DANGEROUS)
        'max_rows' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
        'key' => 'nalrep_schema_v1',
    ],
];
