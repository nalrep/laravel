<?php

namespace Nalrep\Exceptions;

/**
 * Thrown when the AI cannot generate a valid query
 */
class QueryGenerationException extends NalrepException
{
    public function __construct(string $message = "Unable to generate a valid query from your request. Please try rephrasing or providing more details.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
