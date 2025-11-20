<?php

namespace Nalrep\Exceptions;

/**
 * Thrown when query validation fails (e.g., destructive operations detected)
 */
class ValidationException extends NalrepException
{
    public function __construct(string $message = "The generated query failed security validation.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
