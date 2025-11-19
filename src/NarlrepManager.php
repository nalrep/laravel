<?php

namespace Narlrep;

use Illuminate\Support\Facades\Log;
use Narlrep\Schema\SchemaManager;
use Narlrep\AI\Agent;
use Narlrep\Query\Validation;
use Narlrep\Query\Executor;
use Narlrep\Output\Formatter;
use OpenAI;

class NarlrepManager
{
    protected $app;
    protected $schema;
    protected $agent;
    protected $validator;
    protected $executor;
    protected $formatter;

    public function __construct($app)
    {
        $this->app = $app;
        
        // Initialize components
        $this->schema = new SchemaManager();
        
        $this->resolveAgent();
        
        $this->validator = new Validation(config('narlrep.safety', []));
        $this->executor = new Executor();
        $this->formatter = new Formatter();
    }

    protected function resolveAgent()
    {
        $driver = config('narlrep.driver', 'openai');

        if ($driver === 'openai') {
            $apiKey = config('narlrep.openai.api_key');
            $client = OpenAI::client($apiKey ?: 'test-key');
            $this->agent = new \Narlrep\AI\Drivers\OpenAIAgent($client);
        } elseif ($driver === 'openrouter') {
            $apiKey = config('narlrep.openrouter.api_key');
            $model = config('narlrep.openrouter.model');
            $this->agent = new \Narlrep\AI\Drivers\OpenRouterAgent($apiKey, $model);
        } elseif (class_exists($driver)) {
            // Custom driver class
            $this->agent = new $driver();
        } else {
            throw new \Exception("Unknown Narlrep driver: {$driver}");
        }
    }

    public function generate(string $prompt, string $format = 'html')
    {
        // 1. Get Schema
        $schemaSummary = $this->schema->getSchemaSummary();

        Log::info(json_encode($schemaSummary, JSON_PRETTY_PRINT));
        
        // 2. Generate Query via AI
        $this->agent->setSchema($schemaSummary);
        $queryCode = $this->agent->generateQuery($prompt);
        
        // 3. Validate
        $this->validator->validate($queryCode);
        
        // 4. Execute
        $results = $this->executor->execute($queryCode);
        
        // 5. Format
        return $this->formatter->format($results, $format);
    }
}
