<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotModelException extends Exception
{
    public function __construct($type, $code = 0, Throwable $previous = null)
    {
        $message = $type . ' isn\'t an Eloquent model. Must be an instance of \Illuminate\Database\Eloquent\Model.';

        parent::__construct($message, $code, $previous);
    }
}
