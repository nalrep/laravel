<?php

namespace Nalrep\AI\Drivers;

use Nalrep\Contracts\Agent;
use OpenAI\Client;

class OpenAIAgent implements Agent
{
    protected $client;
    protected $schema;

    protected $models = [];
    protected $model;

    public function __construct(Client $client, string $model)
    {
        if (empty($model)) {
            throw new \InvalidArgumentException('Model must be provided.');
        }

        $this->client = $client;
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
        // Placeholder for AI interaction
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

        $response = $this->client->chat()->create([
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

        return $response->choices[0]->message->content;
    }
}
