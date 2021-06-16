<?php declare(strict_types=1);

namespace App\Services;

interface VcsProviderInterface
{
    /**
     * Check if provided credentials are valid by trying some auth-protected GET request.
     */
    public function credentialsAreValid(): bool;
}
