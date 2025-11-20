<?php

namespace Nalrep\Exceptions;

/**
 * Thrown when the AI returns invalid or malformed JSON
 */
class InvalidJsonException extends NalrepException
{
    public function __construct(string $message = "The AI returned invalid JSON. Please try rephrasing your query.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
