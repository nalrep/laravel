<?php

namespace Nalrep\Contracts;

interface Agent
{
    /**
     * Set the database schema context for the agent.
     *
     * @param array $schema
     * @return self
     */
    public function setSchema(array $schema): self;

    /**
     * Set the models context for the agent.
     *
     * @param array $models
     * @return self
     */
    public function setModels(array $models): self;

    /**
     * Generate a query based on the prompt.
     *
     * @param string $prompt
     * @param string $mode
     * @return string
     */
    public function generateQuery(string $prompt, string $mode = 'builder'): string;
}
