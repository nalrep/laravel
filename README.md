# Nalrep

**Nalrep** is a powerful Laravel package that enables **Natural Language Reporting** for your application. It leverages AI (OpenAI, OpenRouter, or custom drivers) to intelligently understand your database schema and generate safe, accurate SQL or Query Builder queries from plain English prompts.

Give your users the power to ask questions like *"Show me total sales by product category for last month"* and get instant results, visualized beautifully.

## Features

*   **Natural Language to Data**: Convert English questions into database queries.
*   **Schema Aware**: Automatically inspects your database schema (tables, columns) to provide context to the AI.
*   **Smart Filtering**: Strictly scopes schema scanning to your application's database, ignoring system tables.
*   **Safety First**: Enforces read-only queries (SELECT only) and validates generated code to prevent destructive operations.
*   **Multiple AI Drivers**:
    *   **OpenAI**: Native support for OpenAI's GPT models.
    *   **OpenRouter**: Access a wide range of models (Claude, Llama, Mistral) via OpenRouter.
    *   **Custom**: Implement your own driver easily.
*   **Smart Formatting**: Automatically detects data structures to render results as:
    *   **Tables**: For structured datasets.
    *   **Lists**: For simple collections.
    *   **Text**: For single values or summaries.
    *   **PDF**: Export reports to PDF (via dompdf).
*   **Ready-to-Use UI**: Includes a drop-in Blade component `<x-nalrep::input />` for instant integration.

## Installation

1.  **Require the package**:
    ```bash
    composer require nalrep/nalrep
    ```

2.  **Publish Configuration**:
    ```bash
    php artisan vendor:publish --tag=config --provider="Nalrep\NalrepServiceProvider"
    ```

3.  **Configure Environment**:
    Add the following to your `.env` file:

    ```env
    # Choose Driver: openai, openrouter, or custom class
    NALREP_DRIVER=openrouter

    # For OpenAI
    OPENAI_API_KEY=sk-...
    NALREP_OPENAI_MODEL=gpt-4o

    # For OpenRouter
    OPENROUTER_API_KEY=sk-or-...
    NALREP_OPENROUTER_MODEL=openai/gpt-3.5-turbo
    ```

### Configuration

You can customize the schema scanning behavior in `config/nalrep.php`:

```php
// Exclude specific tables from being sent to the AI
'excluded_laravel_tables' => '*', // '*' excludes all default Laravel tables, or provide an array ['migrations', 'users']
'excluded_tables' => ['audit_logs', 'admin_users'], // Custom tables to exclude
```

## Usage

### 1. Using the Blade Component

The easiest way to use Nalrep is to drop the input component into any Blade view:

```blade
<x-nalrep::input />
```

This renders a search bar where users can type their questions. The results will be displayed automatically.

### 2. Programmatic Usage

You can use the `Nalrep` facade to generate reports programmatically:

```php
use Nalrep\Facades\Nalrep;

$report = Nalrep::generate("What are the top 5 selling products?");

// Returns HTML string
echo $report;
```

### 3. Customizing the Agent

You can create a custom AI agent by implementing the `Nalrep\Contracts\Agent` interface:

```php
namespace App\AI;

use Nalrep\Contracts\Agent;

class MyCustomAgent implements Agent
{
    public function setSchema(array $schema): Agent
    {
        // Store schema
        return $this;
    }

    public function generateQuery(string $prompt): string
    {
        // Call your AI service here
        return "DB::table('users')->get();";
    }
}
```

Then update your `.env`:
```env
NALREP_DRIVER=App\AI\MyCustomAgent
```

## Security

Nalrep is designed with security in mind:
*   **Read-Only**: The query validator blocks `DELETE`, `UPDATE`, `INSERT`, `DROP`, `ALTER`, and other destructive keywords.
*   **Sanitization**: AI-generated code is sanitized to remove PHP tags and potentially unsafe function calls before execution.
*   **Schema Scoping**: The schema scanner is strictly scoped to your configured database connection to prevent leaking information about other databases on the same server.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
