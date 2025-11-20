<?php

namespace Nalrep\AI\Drivers;

use Illuminate\Support\Facades\Log;
use Nalrep\Contracts\Agent;
use Nalrep\AI\PromptBuilder;
use Illuminate\Support\Facades\Http;

class OpenRouterAgent implements Agent
{
    protected $client;
    protected $schema;
    protected $models = [];
    protected $apiKey;
    protected $model;
    protected $timeout;

    public function __construct(string $apiKey, string $model, int $timeout = 120)
    {
        if (empty($model)) {
            throw new \InvalidArgumentException('Model must be provided.');
        }

        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->timeout = $timeout;
    }

    public function setSchema(array $schema): Agent
    {
        $this->schema = $schema;
        return $this;
    }

    public function setModels(array $models): Agent
    {
        $this->models = $models;
        return $this;
    }

    public function generateQuery(string $prompt, string $mode = 'builder'): string
    {
        $currentDate = date('Y-m-d');

        $promptBuilder = new PromptBuilder();
        $systemPrompt = $promptBuilder->build($this->schema, $this->models, $currentDate);

        // OpenRouter uses OpenAI-compatible API
        $response = Http::timeout($this->timeout)->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'HTTP-Referer' => config('app.url'), // Required by OpenRouter
            'X-Title' => config('app.name'), // Optional
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
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
        
        // Log::info("AI builder: $content");

        return trim($code);
    }
}
