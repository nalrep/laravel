<?php

namespace Nalrep\Exceptions;

/**
 * Thrown when the user's prompt is too vague or unclear
 */
class VaguePromptException extends NalrepException
{
    public function __construct(string $message = "Your query is too vague. Please be more specific about what data you want to see.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
