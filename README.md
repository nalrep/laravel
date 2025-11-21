# Nalrep: Natural Language Reporting for Laravel

[![Latest Stable Version](https://img.shields.io/packagist/v/nalrep/laravel.svg?style=flat&color=4CAF50)](https://packagist.org/packages/nalrep/laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/nalrep/laravel.svg?style=flat&color=2196F3)](https://packagist.org/packages/nalrep/laravel)
[![License](https://img.shields.io/packagist/l/nalrep/laravel.svg?style=flat&color=795548)](LICENSE)


**Nalrep** (Natural Language Reporting) is a powerful Laravel package that empowers your application with AI-driven reporting capabilities. It allows users to generate complex database reports using simple natural language prompts, converting them into safe, executable Laravel Query Builder code.

---

## 📑 Table of Contents

- [Example Report](#-example-report)
- [Features](#-features)
- [How It Works Internally](#-how-it-works-internally)
- [Installation](#-installation)
- [Configuration](#️-configuration)
  - [AI Provider](#1-ai-provider)
  - [Request Timeout](#2-request-timeout)
  - [Schema Exclusion](#3-schema-exclusion)
  - [Model Scanning](#4-model-scanning)
  - [Common Classes (Auto-Imports)](#5-common-classes-auto-imports)
  - [Frontend Component Settings](#6-frontend-component-settings)

  - [Safety Settings](#8-safety-settings)
- [Security Architecture](#-security-architecture)
- [Usage](#-usage)
  - [Blade Component](#blade-component)
  - [Customizing the View](#customizing-the-view)
  - [Programmatic Usage](#programmatic-usage)
- [Error Handling](#️-error-handling)
  - [Exception Types](#exception-types)
  - [Handling Errors in Your Code](#handling-errors-in-your-code)
  - [How Errors Are Surfaced](#how-errors-are-surfaced)
  - [AI Error Detection](#ai-error-detection)
- [Extensibility](#-extensibility)
  - [Custom AI Agents](#custom-ai-agents)
  - [Using PromptBuilder Standalone](#using-promptbuilder-standalone)
- [License](#-license)

---

## 📊 Example Report

![Sample Report](https://github.com/user-attachments/assets/f3dc22b8-173d-47dd-8219-440e3827680a)

*Generate beautiful reports from natural language queries like "Top 5 customers by total purchase revenue"*

![JSON Report](https://github.com/user-attachments/assets/dd9fa530-5bc2-4d90-a687-e97af0067c67)

*Get clean JSON responses for API integrations: "give me list of all users with count of customers served and sales made"*

---

## 🚀 Features

-   **Natural Language to SQL**: Convert questions like "Show me top selling products last month" into database queries.
-   **Safe Execution**: Built-in validation ensures only read-only queries are executed.
-   **Context-Aware**: Automatically scans your database schema and Eloquent models to provide the AI with accurate context.
-   **Eloquent Integration**: Intelligently uses your application's Eloquent models (e.g., `\\App\\Models\\Sale`) when available.
-   **Flexible Output**: Returns results as JSON or HTML tables. HTML reports can be printed to PDF using browser's print function.
-   **Multi-Provider Support**: Works with OpenAI, OpenRouter, and Ollama (local LLMs).
-   **Highly Configurable**: Fine-tune schema visibility, model scanning, and auto-imports.

---

## � How It Works Internally

```
User Prompt
   ↓
PromptBuilder → AI Model
   ↓
JSON Query Definition
   ↓
Validator (read-only, method whitelist)
   ↓
Interpreter → Laravel Query Builder
   ↓
Result (HTML / JSON)
```

---

## �📦 Installation

1.  **Require the package** via Composer:
    ```bash
    composer require nalrep/laravel
    ```

2.  **Publish the configuration** file:
    ```bash
    php artisan vendor:publish --tag=config --provider="Nalrep\\NalrepServiceProvider"
    ```

---

## ⚙️ Configuration

The configuration file `config/nalrep.php` gives you full control over how Nalrep behaves.

### 1. AI Provider
Choose your AI driver. Supported drivers: `openai`, `openrouter`, `ollama`.

```php
'driver' => env('NALREP_DRIVER', 'openai'),

'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'model' => env('NALREP_OPENAI_MODEL', 'gpt-4o-mini'), // Required
],

'openrouter' => [
    'api_key' => env('OPENROUTER_API_KEY'),
    'model' => env('NALREP_OPENROUTER_MODEL', 'openai/gpt-4o-mini'), // Required
],
```

**Example Models:**
  - `gpt-4o-mini` - Fast and cost-effective (recommended for most use cases)
  - `gpt-4o` - More powerful for complex queries
  - `o3-mini` - Optimized for code generation

### 2. Request Timeout
Set the maximum duration for AI requests to prevent hanging processes.

```php
// Timeout in seconds (default: 120)
'timeout' => env('NALREP_TIMEOUT', 120),
```

### 3. Schema Exclusion
Control which database tables are visible to the AI. This is crucial for security and token optimization.

```php
// Tables to exclude from the schema sent to the AI
'excluded_laravel_tables' => [
    'migrations', 'failed_jobs', 'password_reset_tokens', 'sessions', 
    'cache', 'cache_locks', 'jobs', 'job_batches', 'sqlite_sequence'
],

'excluded_tables' => [
    'admin_users', 'audit_logs', 'sensitive_data'
],
```

### 4. Model Scanning
Nalrep scans your application for Eloquent models to help the AI write cleaner, more "Laravel-like" queries using your actual classes.

```php
// Directories to scan for Eloquent models
'model_paths' => [
    'app/Models',
],
```
*The AI is instructed to use the Fully Qualified Class Name (FQCN) (e.g., `\\App\\Models\\User`) to avoid "Class Not Found" errors.*

### 5. Common Classes (Auto-Imports)
Define classes that should be automatically available in the generated code execution environment. This prevents common "Class not found" errors.

```php
'common_classes' => [
    'Carbon\\Carbon',
    'Illuminate\\Support\\Facades\\DB',
    // Add any other classes your queries might need
],
```
*These classes will be automatically available when executing queries, allowing the AI to use them without import statements.*

### 6. Frontend Component Settings
Configure the input component behavior and available formats.

```php
'allowed_formats' => ['html', 'json', 'pdf'], // Formats available in the dropdown
'example_prompts' => [
    'Total sales last month',
    'Top 5 customers by revenue',
    'New users this week',
],
```

### 7. Safety Settings
Configure the safety guardrails.

```php
'safety' => [
    'allow_destructive' => false, // MUST be false in production. Blocks DELETE, UPDATE, DROP, etc.
    'max_rows' => 1000,           // Limit result set size to prevent memory exhaustion.
],
```

---

## 🔒 Security Architecture

Nalrep takes security seriously. We use a **JSON-based Query Interpreter** to ensure safe execution.

### 1. No `eval()`
We do **not** use PHP's `eval()` function. Instead, the AI generates a structured JSON definition of the query (e.g., `{"method": "where", "args": [...]}`). This JSON is parsed and executed by a strict interpreter that only allows valid Query Builder methods.

### 2. Read-Only Enforcement
The **Validator** inspects the JSON structure before execution and blocks any destructive methods such as `delete`, `update`, `insert`, `drop`, or `truncate`.

### 3. Schema Filtering
By using `excluded_tables`, you ensure that the AI never sees the structure of sensitive tables.

### 4. Static Date Generation
The AI is provided with the current date and generates static date strings (e.g., "2024-01-01"), eliminating the need to execute arbitrary PHP date logic.

---

## 💻 Usage

### Blade Component
The easiest way to use Nalrep is via the provided Blade component. It renders a simple input form for users to type their request.

```blade
<x-nalrep::input />
```

### Customizing the View
You can publish the views to customize the frontend component:
```bash
php artisan vendor:publish --tag=views --provider="Nalrep\\NalrepServiceProvider"
```
This will publish the views to `resources/views/vendor/nalrep`. You can edit `components/input.blade.php` to match your application's design.

### Programmatic Usage
You can use the `Nalrep` facade to generate reports programmatically in your controllers or commands.

```php
use Nalrep\\Facades\\Nalrep;

public function report()
{
    $prompt = "Show me the total sales for each product in 2024";
    
    // Returns HTML string of the report table
    $html = Nalrep::generate($prompt, 'html');
    
    return view('reports.show', compact('html'));
}
```



---

## ⚠️ Error Handling

Nalrep provides comprehensive error handling to help developers and users understand what went wrong.

### Exception Types

Nalrep throws specific exceptions for different error scenarios:

```php
use Nalrep\Exceptions\{
    NalrepException,           // Base exception
    VaguePromptException,      // Prompt is too unclear
    InvalidPromptException,    // Prompt not related to data reporting
    QueryGenerationException,  // Cannot generate valid query
    InvalidJsonException,      // AI returned malformed JSON
    ValidationException        // Query failed security validation
};
```

### Handling Errors in Your Code

```php
use Nalrep\Facades\Nalrep;
use Nalrep\Exceptions\VaguePromptException;
use Nalrep\Exceptions\InvalidPromptException;

try {
    $report = Nalrep::generate($userPrompt, 'html');
} catch (VaguePromptException $e) {
    // User's query was too vague
    return back()->with('error', $e->getMessage());
} catch (InvalidPromptException $e) {
    // User's query wasn't related to data reporting
    return back()->with('error', 'Please ask for a data report or query.');
} catch (\Nalrep\Exceptions\NalrepException $e) {
    // Any other Nalrep-specific error
    \Log::warning('Nalrep error', ['message' => $e->getMessage()]);
    return back()->with('error', 'Unable to generate report. Please try rephrasing your query.');
}
```

### How Errors Are Surfaced

**In Development (`APP_DEBUG=true`):**
- Full error messages are displayed
- Stack traces are available in logs
- JSON responses include detailed error information

**In Production (`APP_DEBUG=false`):**
- User-friendly error messages are shown
- Technical details are logged but not exposed
- Generic fallback messages for unexpected errors

### AI Error Detection

The AI is instructed to detect and report issues:

- **Vague Prompts**: "Show me data" → AI returns `vague_prompt` error
- **Invalid Requests**: "What's the weather?" → AI returns `invalid_prompt` error  
- **Schema Mismatch**: "Show sales from products table" (when table doesn't exist) → AI returns `query_generation_failed` error

### Example Error Responses

**JSON Format:**
```json
{
  "error": true,
  "type": "VaguePromptException",
  "message": "Your query is too vague. Please be more specific about what data you want to see."
}
```

**HTML Format:**
```html
<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
  <strong>Unable to generate report:</strong> Your query is too vague...
</div>
```

---

## 🔌 Extensibility

### Custom AI Agents
You can implement your own AI driver by implementing the `Nalrep\\Contracts\\Agent` interface and registering it in the config.

```php
use Nalrep\\Contracts\\Agent;
use Nalrep\\AI\\PromptBuilder;

class MyCustomAgent implements Agent {
    protected $schema;
    protected $models;
    
    public function setSchema(array $schema): Agent {
        $this->schema = $schema;
        return $this;
    }
    
    public function setModels(array $models): Agent {
        $this->models = $models;
        return $this;
    }
    
    public function generateQuery(string $prompt, string $mode = 'builder'): string {
        // Use the built-in PromptBuilder
        $promptBuilder = new PromptBuilder();
        
        // Optionally add custom instructions
        $promptBuilder->appendCustomInstructions(
            "Always include a summary row at the end of results."
        );
        
        $systemPrompt = $promptBuilder->build(
            $this->schema,
            $this->models,
            date('Y-m-d')
        );
        
        // Send to your custom AI model (e.g., local Ollama, Anthropic, etc.)
        $aiCompletion = $this->myAiService->complete($systemPrompt, $prompt);
        
        // Return the JSON query definition from the AI
        return $aiCompletion;
    }
}

// config/nalrep.php
'driver' => MyCustomAgent::class,
```

### Using PromptBuilder Standalone
Developers can also use the `PromptBuilder` class directly for custom implementations:

```php
use Nalrep\\AI\\PromptBuilder;

$builder = new PromptBuilder();

// Get just the base prompt
$basePrompt = $builder->getBasePrompt();

// Or build the complete prompt
$fullPrompt = $builder->build($schema, $models, date('Y-m-d'));

// Add custom instructions
$builder->appendCustomInstructions("Focus on performance optimization.");
$customPrompt = $builder->build($schema, $models, date('Y-m-d'));
```

---

## 📄 License

The MIT License (MIT).
