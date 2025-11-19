<?php

namespace Nalrep\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class SchemaManager
{
    protected $connection;

    public function __construct($connection = null)
    {
        $this->connection = $connection;
    }

    public function getSchemaSummary()
    {
        return Cache::remember(config('nalrep.cache.key', 'nalrep_schema_v1'), config('nalrep.cache.ttl', 3600), function () {
            return $this->introspectDatabase();
        });
    }

    protected function introspectDatabase()
    {
        $tables = $this->getTables();
        $schema = [];

        foreach ($tables as $table) {
            $schema[$table] = [
                'columns' => $this->getColumns($table),
                'foreign_keys' => $this->getForeignKeys($table),
            ];
        }

        return $schema;
    }

    protected function getTables()
    {
        $connection = Schema::connection($this->connection)->getConnection();
        $dbName = $connection->getDatabaseName();
        $driver = $connection->getDriverName();

        // For MySQL, we can be explicit to avoid getting tables from other DBs if the user has broad permissions
        if ($driver === 'mysql') {
            $results = $connection->select('SELECT TABLE_NAME as name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$dbName]);
            return array_map(fn($row) => $row->name, $results);
        }

        $tables = Schema::connection($this->connection)->getTableListing();

        // Get exclusion configs
        $excludedLaravelTables = config('nalrep.excluded_laravel_tables', []);
        $excludedCustomTables = config('nalrep.excluded_tables', []);

        // Determine which Laravel tables to exclude
        $tablesToExclude = array_merge($excludedCustomTables, $excludedLaravelTables);

        // Filter tables
        $tables = array_values(array_filter($tables, function ($table) use ($tablesToExclude) {
            return !in_array($table, $tablesToExclude);
        }));

        return $tables;
    }

    protected function getColumns($table)
    {
        $columns = Schema::connection($this->connection)->getColumns($table);
        
        return array_map(function ($column) {
            return [
                'name' => $column['name'],
                'type' => $column['type_name'], // Using type_name as 'type' for simpler output
            ];
        }, $columns);
    }

    protected function getForeignKeys($table)
    {
        $connection = Schema::connection($this->connection)->getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $keys = $connection->select("PRAGMA foreign_key_list({$table})");
            return array_map(function($key) {
                return [
                    'column' => $key->from,
                    'foreign_table' => $key->table,
                    'foreign_column' => $key->to,
                ];
            }, $keys);
        }

        if ($driver === 'mysql') {
            $dbName = $connection->getDatabaseName();
            $keys = $connection->select("
                SELECT 
                    COLUMN_NAME as `column`, 
                    REFERENCED_TABLE_NAME as foreign_table, 
                    REFERENCED_COLUMN_NAME as foreign_column 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$dbName, $table]);

            return array_map(function($key) {
                return [
                    'column' => $key->column,
                    'foreign_table' => $key->foreign_table,
                    'foreign_column' => $key->foreign_column,
                ];
            }, $keys);
        }

        // Fallback or other drivers can be added here
        return [];
    }
}
