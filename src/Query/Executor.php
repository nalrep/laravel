<?php

namespace Nalrep\Query;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Executor
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function execute(string $query, string $mode = 'builder')
    {
        Log::info("query: $query");

        if ($mode === 'builder') {
            return $this->executeJson($query);
        }

        return $this->executeSql($query);
    }

    protected function executeJson(string $json)
    {
        // Clean JSON string if it contains markdown
        $json = str_replace(['```json', '```'], '', $json);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON query: " . json_last_error_msg());
        }

        // Resolve Root (Model or DB Table)
        if (isset($data['model']) && class_exists($data['model'])) {
            $query = $data['model']::query();
        } elseif (isset($data['table'])) {
            $query = DB::table($data['table']);
        } else {
            throw new \Exception("Query must specify a 'model' or 'table'.");
        }

        // Apply Steps
        if (isset($data['steps']) && is_array($data['steps'])) {
            $this->applySteps($query, $data['steps']);
        }

        // Check if the result is still a Builder or Relation (meaning no finisher was called)
        if ($query instanceof \Illuminate\Database\Eloquent\Builder || 
            $query instanceof \Illuminate\Database\Query\Builder ||
            $query instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            return $query->get();
        }

        return $query;
    }

    protected function applySteps(&$query, array $steps)
    {
        foreach ($steps as $step) {
            $method = $step['method'];
            $args = $step['args'] ?? [];
            
            // Process Args (handle closures and raw)
            $processedArgs = array_map(function ($arg) {
                return $this->processArg($arg);
            }, $args);

            // Call method
            // We update $query because some methods return a new instance or the result
            $query = $query->{$method}(...$processedArgs);
        }
    }

    protected function processArg($arg)
    {
        if (is_array($arg) && isset($arg['type'])) {
            if ($arg['type'] === 'raw') {
                return DB::raw($arg['value']);
            }
            if ($arg['type'] === 'closure') {
                return function ($q) use ($arg) {
                    $this->applySteps($q, $arg['steps']);
                };
            }
        }
        return $arg;
    }

    protected function executeSql(string $sql)
    {
        return DB::select($sql);
    }
}
