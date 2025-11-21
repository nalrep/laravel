<?php

namespace Nalrep\AI;

/**
 * PromptBuilder - Constructs system prompts for AI agents
 * 
 * This class provides a reusable way to build system prompts for AI models.
 * Developers can use this with custom agents or extend it with additional instructions.
 * 
 * @example
 * $builder = new PromptBuilder();
 * $prompt = $builder->build($schema, $models, date('Y-m-d'));
 * 
 * @example Custom instructions
 * $builder = new PromptBuilder();
 * $builder->appendCustomInstructions("Always include a summary row at the end.");
 * $prompt = $builder->build($schema, $models, date('Y-m-d'));
 */
class PromptBuilder
{
    protected string $customInstructions = '';

    /**
     * Build the complete system prompt
     * 
     * @param array $schema Database schema information
     * @param array $models Available Eloquent models
     * @param string $currentDate Current date in Y-m-d format
     * @return string Complete system prompt
     */
    public function build(array $schema, array $models, string $currentDate): string
    {
        $prompt = $this->getBasePrompt();
        $prompt .= "Current Date: $currentDate\n";
        $prompt .= "Schema: " . json_encode($schema) . "\n";
        $prompt .= "Models: " . json_encode($models) . "\n";
        $prompt .= $this->getOutputFormat();
        $prompt .= $this->getSpecialInstructions();
        
        if (!empty($this->customInstructions)) {
            $prompt .= "\n" . $this->customInstructions;
        }
        
        return $prompt;
    }

    /**
     * Get the base prompt without schema/models
     * Useful for developers who want to build their own prompt structure
     * 
     * @return string Base system prompt
     */
    public function getBasePrompt(): string
    {
        return "You are a Laravel expert. Generate a JSON object that describes a safe database query based on the user request and schema.\n";
    }

    /**
     * Get the output format specification
     * 
     * @return string Output format instructions
     */
    public function getOutputFormat(): string
    {
        $format = "Output Format (JSON):\n";
        $format .= "{\n";
        $format .= "  \"description\": \"A human-readable sentence explaining what this query returns, including date ranges when applicable (e.g., 'Here is the total sales for last month (2024-10-01 to 2024-10-31)')\",\n";
        $format .= "  \"model\": \"Fully Qualified Class Name\" (e.g., \"App\\\\Models\\\\User\") OR \"table\": \"table_name\",\n";
        $format .= "  \"steps\": [\n";
        $format .= "    { \"method\": \"where\", \"args\": [\"status\", \"active\"] },\n";
        $format .= "    { \"method\": \"orderBy\", \"args\": [\"created_at\", \"desc\"] }\n";
        $format .= "  ]\n";
        $format .= "}\n\n";
        
        $format .= "IMPORTANT QUERY BUILDING RULES:\n";
        $format .= "1. DO NOT use withCount(), with(), or any relationship methods unless you are 100% certain the relationship exists\n";
        $format .= "2. For aggregations (counts, sums) from related tables, use 'select' with raw SQL subqueries instead\n";
        $format .= "3. Example for counting related records:\n";
        $format .= "   { \"method\": \"select\", \"args\": [\"*\", {\"type\": \"raw\", \"value\": \"(SELECT COUNT(*) FROM sales WHERE sales.user_id = users.id) AS sales_count\"}] }\n";
        $format .= "4. For DISTINCT counts:\n";
        $format .= "   { \"method\": \"select\", \"args\": [\"*\", {\"type\": \"raw\", \"value\": \"(SELECT COUNT(DISTINCT buyer_id) FROM sales WHERE sales.user_id = users.id) AS customers_count\"}] }\n";
        $format .= "5. Always use table names explicitly in subqueries (e.g., 'users.id', 'sales.user_id')\n\n";
        
        $format .= "ERROR HANDLING:\n";
        $format .= "If the user's request is unclear, too vague, or not related to data reporting, return an error response:\n";
        $format .= "{\n";
        $format .= "  \"error\": \"vague_prompt\" | \"invalid_prompt\" | \"query_generation_failed\",\n";
        $format .= "  \"message\": \"A helpful message explaining the issue\"\n";
        $format .= "}\n\n";
        $format .= "Error types:\n";
        $format .= "- vague_prompt: The request is too unclear or lacks specificity\n";
        $format .= "- invalid_prompt: The request is not related to data reporting or database queries\n";
        $format .= "- query_generation_failed: You cannot generate a valid query from the available schema\n";
        
        return $format;
    }

    /**
     * Get special instructions for handling complex queries
     * 
     * @return string Special instructions
     */
    public function getSpecialInstructions(): string
    {
        $instructions = "For DB::raw(), use object: { \"type\": \"raw\", \"value\": \"SUM(price)\" } as an argument.\n";
        $instructions .= "For Closures (nested logic), use object: { \"type\": \"closure\", \"steps\": [...] } as an argument.\n";
        $instructions .= "IMPORTANT: Return ONLY the JSON string. No markdown, no explanations.";
        
        return $instructions;
    }

    /**
     * Append custom instructions to the prompt
     * 
     * @param string $instructions Custom instructions to add
     * @return self
     */
    public function appendCustomInstructions(string $instructions): self
    {
        $this->customInstructions .= "\n" . $instructions;
        return $this;
    }

    /**
     * Clear custom instructions
     * 
     * @return self
     */
    public function clearCustomInstructions(): self
    {
        $this->customInstructions = '';
        return $this;
    }
}
