<?php

namespace App\Validation\Exceptions;

use Exception;
use Throwable;

class RuleAlreadyAdded extends Exception
{
    public function __construct($rule = "", $code = 0, Throwable $previous = null)
    {
        $message = 'The rule "' . $rule. '" was already added to this Rules instance. It may have been added with different attributes, which is not supported right now.';

        parent::__construct($message, $code, $previous);
    }
}
