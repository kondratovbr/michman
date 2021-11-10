<?php declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\AuthTokenDto;

interface ProviderInterface
{
    /** Refresh the authentication token if applicable. */
    public function refreshToken(): AuthTokenDto;
}
