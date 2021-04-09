<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait UsesBasicAuth
{
    /**
     * Get key (login) to use for authentication.
     */
    abstract function getKey(): string;

    /**
     * Get secret (password) to use for authentication.
     */
    abstract function getSecret(): string;

    /**
     * Crete a pending request with authentication configured.
     */
    protected function request(): PendingRequest
    {
        return Http::withBasicAuth($this->getKey(), $this->getSecret());
    }
}
