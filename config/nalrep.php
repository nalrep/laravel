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
    'driver' => env('NALREP_DRIVER', 'openai'),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('NALREP_OPENAI_MODEL', 'gpt-4-turbo'),
    ],

    'ollama' => [
        'api_url' => env('OLLAMA_API_URL', 'http://localhost:11434/api/generate'),
        'model' => env('NALREP_OLLAMA_MODEL', 'llama3'),
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('NALREP_OPENROUTER_MODEL', 'openai/gpt-3.5-turbo'),
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
    | Schema Exclusion
    |--------------------------------------------------------------------------
    |
    | Define tables that should be excluded from the schema sent to the AI.
    |
    | 'excluded_laravel_tables':
    |   - '*' : Exclude all default Laravel tables (migrations, jobs, etc.)
    |   - []  : Do not exclude any default tables
    |   - ['migrations', 'users'] : Exclude specific default tables
    |
    | 'excluded_tables':
    |   - List of custom table names to exclude.
    |
    */
    'excluded_laravel_tables' => [
        'migrations', 'failed_jobs', 'password_reset_tokens', 'sessions', 
        'cache', 'cache_locks', 'jobs', 'job_batches', 'sqlite_sequence'
    ],
    'excluded_tables' => [], // Custom tables to exclude

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
