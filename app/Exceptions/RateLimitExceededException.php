<?php

namespace App\Exceptions;

use Exception;

class RateLimitExceededException extends Exception
{
    /**
     * Create a new rate limit exceeded exception.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "Too many attempts", int $code = 429, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
