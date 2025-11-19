# Nalrep: Natural Language Reporting for Laravel

**Nalrep** (Natural Language Reporting) is a powerful Laravel package that empowers your application with AI-driven reporting capabilities. It allows users to generate complex database reports using simple natural language prompts, converting them into safe, executable Laravel Query Builder code.

---

## 🚀 Features

-   **Natural Language to SQL**: Convert questions like "Show me top selling products last month" into database queries.
-   **Safe Execution**: Built-in validation ensures only read-only queries are executed.
-   **Context-Aware**: Automatically scans your database schema and Eloquent models to provide the AI with accurate context.
-   **Eloquent Integration**: Intelligently uses your application's Eloquent models (e.g., `\App\Models\Sale`) when available.
-   **Flexible Output**: Returns results as JSON, HTML tables, or PDF reports.
-   **Multi-Provider Support**: Works with OpenAI, OpenRouter, and Ollama (local LLMs).
-   **Highly Configurable**: Fine-tune schema visibility, model scanning, and auto-imports.

---

## 📦 Installation

1.  **Require the package** via Composer:
    ```bash
    composer require nalrep/laravel
    ```

2.  **Publish the configuration** file:
    ```bash
    php artisan vendor:publish --tag=config --provider="Nalrep\NalrepServiceProvider"
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
    'model' => env('NALREP_OPENAI_MODEL', 'gpt-4-turbo'),
],
```

### 2. Schema Exclusion
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

### 3. Model Scanning
Nalrep scans your application for Eloquent models to help the AI write cleaner, more "Laravel-like" queries using your actual classes.

```php
// Directories to scan for Eloquent models
'model_paths' => [
    'app/Models',
],
```
*The AI is instructed to use the Fully Qualified Class Name (FQCN) (e.g., `\App\Models\User`) to avoid "Class Not Found" errors.*

### 4. Common Classes (Auto-Imports)
Define classes that should be automatically available in the generated code execution environment. This prevents "Class 'Carbon' not found" errors.

```php
'common_classes' => [
    'Carbon\Carbon',
    'Illuminate\Support\Facades\DB',
],

// Frontend Component Settings
'allowed_formats' => ['html', 'json'],
'example_prompts' => [
    'Total sales last month',
    'Top 5 customers by revenue',
    'New users this week',
],
```
*With this config, the AI can write `Carbon::now()` directly.*

### 5. Safety Settings
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
php artisan vendor:publish --tag=views --provider="Nalrep\NalrepServiceProvider"
```
This will publish the views to `resources/views/vendor/nalrep`. You can edit `components/input.blade.php` to match your application's design.

### Programmatic Usage
You can use the `Nalrep` facade to generate reports programmatically in your controllers or commands.

```php
use Nalrep\Facades\Nalrep;

public function report()
{
    $prompt = "Show me the total sales for each product in 2024";
    
    // Returns HTML string of the report table
    $html = Nalrep::generate($prompt, 'html');
    
    return view('reports.show', compact('html'));
}
```

---

## 🔌 Extensibility

### Custom AI Agents
You can implement your own AI driver by implementing the `Nalrep\Contracts\Agent` interface and registering it in the config.

```php
use Nalrep\Contracts\Agent;

class MyCustomAgent implements Agent {
    // ... implementation
}

// config/nalrep.php
'driver' => MyCustomAgent::class,
```

---

## 📄 License

The MIT License (MIT).
