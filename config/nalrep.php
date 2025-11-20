<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "openai", "ollama", "openrouter"
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
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests to AI providers.
    | Default is 120 seconds (2 minutes).
    |
    */
    'timeout' => env('NALREP_TIMEOUT', 120),

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
    */
    'excluded_laravel_tables' => [
        'migrations', 'failed_jobs', 'password_reset_tokens', 'sessions', 
        'cache', 'cache_locks', 'jobs', 'job_batches', 'sqlite_sequence'
    ],
    'excluded_tables' => [], // Custom tables to exclude

    /*
    |--------------------------------------------------------------------------
    | Model Scanning
    |--------------------------------------------------------------------------
    |
    | Define directories to scan for Eloquent models.
    | The AI will be provided with the Fully Qualified Class Name (FQCN)
    | for models found in these directories.
    |
    */
    'model_paths' => [
        'app/Models',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common Classes
    |--------------------------------------------------------------------------
    |
    | Define classes that should be recognized by the JSON interpreter.
    | These are primarily for reference, as the interpreter handles execution safely.
    |
    */
    'common_classes' => [
        'Carbon\Carbon',
        'Illuminate\Support\Facades\DB',
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

    /*
    |--------------------------------------------------------------------------
    | Frontend Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the <x-nalrep::input /> component.
    |
    */
    'allowed_formats' => ['html', 'json', 'pdf'], // Formats available in the frontend dropdown

    'example_prompts' => [
        'Total sales last month',
        'Top 5 customers by revenue',
        'New users this week',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Display Mode
    |--------------------------------------------------------------------------
    |
    | How PDF reports should be displayed:
    | - 'inline': Preview PDF in the browser with download button (recommended)
    | - 'download': Directly download the PDF file
    |
    */
    'pdf_display_mode' => env('NALREP_PDF_DISPLAY_MODE', 'inline'),
];
