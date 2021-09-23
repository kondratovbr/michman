<?php declare(strict_types=1);

namespace App\Http\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Throwable;

class NoWebhookExternalIdProvidedException extends Exception implements Responsable
{
    public function __construct(string|null $message = null, int $code = 0, Throwable $previous = null)
    {
        $message ??= 'The request has no external ID (delivery ID).';

        parent::__construct($message, $code, $previous);
    }

    public function toResponse($request): Response
    {
        return response($this->getMessage(), 400);
    }
}
