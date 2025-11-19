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

        return true;
    }

    protected function validateSql(string $sql)
    {
        $forbidden = ['DELETE', 'UPDATE', 'INSERT', 'DROP', 'TRUNCATE', 'ALTER', 'GRANT', 'REVOKE'];
        
        foreach ($forbidden as $word) {
            if (stripos($sql, $word) !== false) {
                throw new Exception("Destructive SQL command '$word' is not allowed.");
            }
        }

        return true;
    }
}
