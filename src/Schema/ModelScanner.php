<?php

namespace Nalrep\Schema;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class ModelScanner
{
    protected $paths;

    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    public function getModels()
    {
        $models = [];

        foreach ($this->paths as $path) {
            $directory = base_path($path);

            if (!File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $className = $this->getClassFullName($file);

                if ($className && class_exists($className)) {
                    try {
                        $reflection = new ReflectionClass($className);

                        if ($reflection->isSubclassOf(Model::class) && !$reflection->isAbstract()) {
                            $model = new $className;
                            $models[$model->getTable()] = $className;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid classes
                        continue;
                    }
                }
            }
        }

        return $models;
    }

    protected function getClassFullName($file)
    {
        $content = file_get_contents($file->getRealPath());
        
        if (!preg_match('/namespace\s+(.+?);/', $content, $matches)) {
            return null;
        }
        
        $namespace = $matches[1];
        
        if (!preg_match('/class\s+(\w+)/', $content, $matches)) {
            return null;
        }
        
        $className = $matches[1];
        
        return $namespace . '\\' . $className;
    }
}
