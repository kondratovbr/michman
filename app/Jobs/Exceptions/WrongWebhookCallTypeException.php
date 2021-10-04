<?php declare(strict_types=1);

namespace App\Jobs\Exceptions;

use Exception;
use Throwable;

class WrongWebhookCallTypeException extends Exception
{
    public function __construct(string $expectedType, string $receivedType, $code = 0, Throwable $previous = null)
    {
        $message = "Trying to handle a webhook call with a job of a non-matching type. Excepted type: {$expectedType}, call type: {$receivedType}";

        parent::__construct($message, $code, $previous);
    }
}
