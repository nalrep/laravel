<?php

namespace Narlrep\AI\Drivers;

use Illuminate\Support\Facades\Log;
use Narlrep\Contracts\Agent;
use OpenAI\Client;
use Illuminate\Support\Facades\Http;

class OpenRouterAgent implements Agent
{
    protected $client;
    protected $schema;
    protected $apiKey;
    protected $model;

    public function __construct(string $apiKey, string $model = 'openai/gpt-3.5-turbo')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function setSchema(array $schema): Agent
    {
        $this->schema = $schema;
        return $this;
    }

    public function generateQuery(string $prompt, string $mode = 'builder'): string
    {
        // OpenRouter uses OpenAI-compatible API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'HTTP-Referer' => config('app.url'), // Required by OpenRouter
            'X-Title' => config('app.name'), // Optional
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a Laravel expert. Generate a safe Query Builder code snippet based on the user request and database schema. Schema: " . json_encode($this->schema)
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception("OpenRouter API Error: " . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        
        // Extract code block if present
        // Extract code block if present
        if (preg_match('/```php(.*?)```/s', $content, $matches)) {
            $code = trim($matches[1]);
        } else {
            $code = trim($content);
        }

        // Remove <?php opening tag if present
        $code = preg_replace('/^<\?php\s*/', '', $code);

        // Remove use statements as they are not allowed in eval()
        // We assume the necessary facades (DB) are already available or we should use fully qualified names
        // But for now, let's just strip lines starting with 'use '
        $code = preg_replace('/^use\s+.*;$/m', '', $code);
        
        return trim($code);

        Log::info("AI builder: $content");

        return trim($content);
    }
}
