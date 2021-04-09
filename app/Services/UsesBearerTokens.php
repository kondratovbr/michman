<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait UsesBearerTokens
{
    /**
     * Get the bearer token to use for authentication.
     */
    abstract protected function getToken(): string;

    /**
     * Crete a pending request with authentication configured.
     */
    protected function request(): PendingRequest
    {
        return Http::withToken($this->getToken());
    }
}
