<?php

namespace Nalrep\AI\Drivers;

use Illuminate\Support\Facades\Log;
use Nalrep\Contracts\Agent;
use OpenAI\Client;
use Illuminate\Support\Facades\Http;

class OpenRouterAgent implements Agent
{
    protected $client;
    protected $schema;
    protected $models = [];
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

    public function setModels(array $models): Agent
    {
        $this->models = $models;
        return $this;
    }

    public function generateQuery(string $prompt, string $mode = 'builder'): string
    {
        $currentDate = date('Y-m-d');

        $systemPrompt = "You are a Laravel expert. Generate a JSON object that describes a safe database query based on the user request and schema.\n";
        $systemPrompt .= "Current Date: $currentDate\n";
        $systemPrompt .= "Schema: " . json_encode($this->schema) . "\n";
        $systemPrompt .= "Models: " . json_encode($this->models) . "\n";
        $systemPrompt .= "Output Format (JSON):\n";
        $systemPrompt .= "{\n";
        $systemPrompt .= "  \"model\": \"Fully Qualified Class Name\" (e.g., \"App\\\\Models\\\\User\") OR \"table\": \"table_name\",\n";
        $systemPrompt .= "  \"steps\": [\n";
        $systemPrompt .= "    { \"method\": \"where\", \"args\": [\"status\", \"active\"] },\n";
        $systemPrompt .= "    { \"method\": \"orderBy\", \"args\": [\"created_at\", \"desc\"] }\n";
        $systemPrompt .= "  ]\n";
        $systemPrompt .= "}\n";
        $systemPrompt .= "For DB::raw(), use object: { \"type\": \"raw\", \"value\": \"SUM(price)\" } as an argument.\n";
        $systemPrompt .= "For Closures (nested logic), use object: { \"type\": \"closure\", \"steps\": [...] } as an argument.\n";
        $systemPrompt .= "IMPORTANT: Return ONLY the JSON string. No markdown, no explanations.";

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
        
        Log::info("AI builder: $content");

        return trim($code);
    }
}
