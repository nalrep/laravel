<?php

namespace Nalrep\Exceptions;

/**
 * Thrown when the prompt is not related to data reporting
 */
class InvalidPromptException extends NalrepException
{
    public function __construct(string $message = "Your query doesn't appear to be a valid data request. Please ask for specific data or reports from your database.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
