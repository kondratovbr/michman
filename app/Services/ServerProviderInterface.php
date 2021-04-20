<?php declare(strict_types=1);

namespace App\Services;

/*
 * TODO: CRITICAL! Figure out how to avoid hitting the rate limit with these things. Probably move the actual HTTP logic into a singleton for each service, so it can track calls during a request and also between them by using cache. Or maybe just use cache every time?
 */

// TODO: IMPORTANT! Figure out what to do with account statuses. I.e. a provider may lock the account if a payment failed or something. Have to handle it gracefully as well.
// TODO: Maybe figure out how to check if an account reached its servers limit, so I can check it during server creation for the user.

use App\Collections\RegionCollection;
use App\Collections\SizeCollection;
use App\DataTransferObjects\RegionData;
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
     * Get a collection of all regions supported by the API.
     */
    public function getAllRegions(): RegionCollection;

    /**
     * Get a collection of all sizes supported by the API.
     */
    public function getAllSizes(): SizeCollection;

    /**
     * Get a list of regions where this account can create a server.
     */
    public function getAvailableRegions(): RegionCollection;

    /**
     * Get a list of server sizes available for this account.
     */
    public function getAvailableSizes(): SizeCollection;

    /**
     * Get a collection of sizes available for this specific API credentials in a region provided.
     */
    public function getSizesAvailableInRegion(RegionData $region): SizeCollection;
}
