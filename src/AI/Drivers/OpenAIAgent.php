<?php

namespace Nalrep\AI\Drivers;

use Nalrep\Contracts\Agent;
use Nalrep\AI\PromptBuilder;
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
        
        $promptBuilder = new PromptBuilder();
        $systemPrompt = $promptBuilder->build($this->schema, $this->models, $currentDate);

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
