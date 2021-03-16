<?php

namespace App\Validation\Exceptions;

use Exception;
use Throwable;

class InvalidRule extends Exception
{
    public function __construct($rule, $code = 0, Throwable $previous = null)
    {
        $message = 'Only named rules, represented as strings, custom classes implementing Illuminate\Contracts\Validation\Rule or objects that have __toString() method and represent named rules can be added.'
            . 'Tried to add: ' . serialize($rule);

        parent::__construct($message, $code, $previous);
    }
}
