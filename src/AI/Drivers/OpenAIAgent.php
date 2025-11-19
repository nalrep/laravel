<?php

namespace Nalrep\AI\Drivers;

use Nalrep\Contracts\Agent;
use OpenAI\Client;

class OpenAIAgent implements Agent
{
    protected $client;
    protected $schema;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setSchema(array $schema): Agent
    {
        $this->schema = $schema;
        return $this;
    }

    public function generateQuery(string $prompt, string $mode = 'builder'): string
    {
        // Placeholder for AI interaction
        // We will construct a prompt with the schema and the user request
        
        $systemPrompt = "You are a Laravel expert. Generate a safe Query Builder code snippet based on the user request and database schema.";
        
        // Mock response for now
        return "User::all();";
    }
}
