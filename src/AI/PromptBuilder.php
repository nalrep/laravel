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
        $format .= "}\n";
        
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
