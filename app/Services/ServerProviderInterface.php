<?php declare(strict_types=1);

namespace App\Services;

/*
 * TODO: CRITICAL! Figure out how to avoid hitting the rate limit with these things.
 */

// TODO: IMPORTANT! Figure out what to do with account statuses. I.e. a provider may lock the account if a payment failed or something. Have to handle it gracefully as well.
// TODO: Maybe figure out how to check if an account reached its servers limit, so I can check it during server creation for the user.

use App\DataTransferObjects\ServerData;

interface ServerProviderInterface
{
    /**
     * Check if provided credentials are valid by trying some auth-protected GET request.
     */
    public function credentialsAreValid(): bool;

    /**
     * Provision a new server with this server provider.
     *
     * @return string Server ID designated by the provider.
     */
    public function createServer(ServerData $data): string;

    /**
     * Get a list of regions where this account can create a server.
     */
    public function getRegions(): array;
}
