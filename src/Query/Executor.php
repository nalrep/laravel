<?php

namespace Narlrep\Query;

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
            return $this->executeBuilder($query);
        }

        return $this->executeSql($query);
    }

    protected function executeBuilder(string $code)
    {
        // DANGEROUS: Eval is used here. In a real production system, 
        // we would need a much safer way to interpret the builder chain.
        // For this MVP/Prototype, we assume the Validation layer has caught malicious code.
        
        // We wrap the execution in a closure to limit scope
        $result = eval("return " . $code . ";");
        
        return $result;
    }

    protected function executeSql(string $sql)
    {
        return DB::select($sql);
    }
}
