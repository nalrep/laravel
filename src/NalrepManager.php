<?php

namespace Nalrep;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Nalrep\Schema\SchemaManager;
use Nalrep\AI\Agent;
use Nalrep\Query\Validation;
use Nalrep\Query\Executor;
use Nalrep\Output\Formatter;
use OpenAI;
use Nalrep\Schema\ModelScanner;

class NalrepManager
{
    protected $app;
    protected $schema;
    protected $agent;
    protected $validator;
    protected $executor;
    protected $formatter;
    protected $modelScanner;

    public function __construct($app)
    {
        $this->app = $app;
        
        // Initialize components
        $this->schema = new SchemaManager();
        
        $this->resolveAgent();
        
        $this->validator = new Validation(config('nalrep.safety', []));
        $this->modelScanner = new ModelScanner(config('nalrep.model_paths', ['app/Models']));
        $this->executor = new Executor();
        $this->formatter = new Formatter();
    }

    protected function resolveAgent()
    {
        $driver = config('nalrep.driver', 'openai');
        $timeout = config('nalrep.timeout', 120);

        if ($driver === 'openai') {
            $apiKey = config('nalrep.openai.api_key');
            $model = config('nalrep.openai.model');
            
            $factory = OpenAI::factory()
                ->withApiKey($apiKey ?: 'test-key')
                ->withHttpClient(new \GuzzleHttp\Client(['timeout' => $timeout]));

            $client = $factory->make();
            $this->agent = new \Nalrep\AI\Drivers\OpenAIAgent($client, $model);
        } elseif ($driver === 'openrouter') {
            $apiKey = config('nalrep.openrouter.api_key');
            $model = config('nalrep.openrouter.model');
            $this->agent = new \Nalrep\AI\Drivers\OpenRouterAgent($apiKey, $model, $timeout);
        } elseif (class_exists($driver)) {
            // Custom driver class
            $this->agent = new $driver();
        } else {
            throw new \Exception("Unknown Nalrep driver: {$driver}");
        }
    }

    public function generate(string $prompt, string $format = 'html')
    {
        // 1. Get Schema
        $schemaSummary = $this->schema->getSchemaSummary();

        // 2. Get Models
        $models = Cache::remember(config('nalrep.cache.key', 'nalrep_schema_v1') . '_models', config('nalrep.cache.ttl', 3600), function () {
            return $this->modelScanner->getModels();
        });

        // Log::info(json_encode($schemaSummary, JSON_PRETTY_PRINT));
        
        // 3. Generate Query via AI
        $this->agent->setSchema($schemaSummary);
        $this->agent->setModels($models);
        $query = $this->agent->generateQuery($prompt);
        
        // 3. Validate
        $this->validator->validate($query);

        Log::info($query);
        
        // 4. Execute
        $result = $this->executor->execute($query);
        
        // Extract data and description
        $data = $result['data'] ?? $result;
        $description = $result['description'] ?? null;
        
        Log::info('Executor result', [
            'data_type' => gettype($data),
            'data_count' => is_countable($data) ? count($data) : 'N/A',
            'description' => $description,
        ]);
        
        // 5. Format
        $formatted = $this->formatter->format($data, $format, $description);
        
        Log::info('Formatted result', [
            'format' => $format,
            'result_type' => gettype($formatted),
            'result_length' => is_string($formatted) ? strlen($formatted) : 'N/A',
        ]);
        
        return $formatted;
    }
}
