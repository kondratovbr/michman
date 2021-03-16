<?php

namespace App\Validation\Exceptions;

use Exception;
use Throwable;

class UnknownRule extends Exception
{
    public function __construct(string $rule = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Unknown named rule passed: "' . $rule . '"';

        parent::__construct($message, $code, $previous);
    }
}
