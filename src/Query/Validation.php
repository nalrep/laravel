<?php

namespace Nalrep\Query;

use Exception;

class Validation
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function validate(string $query, string $mode = 'builder')
    {
        if ($mode === 'builder') {
            return $this->validateBuilder($query);
        }

        return $this->validateSql($query);
    }

    protected function validateBuilder(string $code)
    {
        // Basic safety checks for Query Builder code
        $forbidden = ['delete', 'update', 'insert', 'drop', 'truncate', 'statement', 'unprepared'];
        
        foreach ($forbidden as $word) {
            if (stripos($code, $word) !== false) {
                // Allow "select" but not "delete"
                // This is a naive check, a real parser would be better
                // But for now, we block these keywords if they appear as method calls
                if (preg_match('/\->\s*' . $word . '/i', $code)) {
                    throw new Exception("Destructive method '$word' is not allowed.");
                }
            }
        }
    }

    protected function validateSql(string $query)
    {
        $forbiddenKeywords = [
            'DELETE', 'DROP', 'TRUNCATE', 'UPDATE', 'INSERT', 'ALTER', 'GRANT', 'REVOKE'
        ];

        $upperQuery = strtoupper($query);

        foreach ($forbiddenKeywords as $keyword) {
            if (strpos($upperQuery, $keyword) !== false) {
                 if (!($this->config['allow_destructive'] ?? false)) { // Added null coalescing for safety
                     throw new \Exception("Destructive query detected: $keyword");
                 }
            }
        }
        return true;
    }
}
