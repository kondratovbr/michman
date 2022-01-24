<?php declare(strict_types=1);

namespace App\Services;

/*
 * TODO: VERY IMPORTANT! Figure out how to avoid hitting the rate limit with these things. Probably move the actual HTTP logic into a singleton for each service, so it can track calls during a request and also between them by using cache. Or maybe just use cache every time?
 */

// TODO: IMPORTANT! Figure out what to do with account statuses. I.e. a provider may lock the account if a payment failed or something. Have to handle it gracefully as well.
// TODO: Maybe figure out how to check if an account reached its servers limit, so I can check it during server creation for the user.

use App\Collections\RegionDataCollection;
use App\Collections\ServerDataCollection;
use App\Collections\SizeDataCollection;
use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\NewServerDto;
use App\DataTransferObjects\RegionDto;
use App\DataTransferObjects\ServerDto;
use App\DataTransferObjects\SshKeyDto;

interface ServerProviderInterface extends ProviderInterface
{
    /** Check if provided credentials are valid by trying some auth-protected GET request. */
    public function credentialsAreValid(): bool;

    /** Get server data by its ID. */
    public function getServer(string $serverId): ServerDto;

    /** Get a collection of all servers created on this account. */
    public function getAllServers(): ServerDataCollection;

    /**
     * Get the public IPv4 address of a server by its ID.
     *
     * Returns null if the address is not yet assigned.
     */
    public function getServerPublicIp4(string $serverId): string|null;

    /** Provision a new server with this server provider. */
    public function createServer(NewServerDto $data, string $sshKeyIdentifier): ServerDto;

    /** Get a collection of all regions supported by the API. */
    public function getAllRegions(): RegionDataCollection;

    /** Get a collection of all sizes supported by the API. */
    public function getAllSizes(): SizeDataCollection;

    /** Get a list of regions where this account can create a server. */
    public function getAvailableRegions(): RegionDataCollection;

    /** Get a list of server sizes available for this account. */
    public function getAvailableSizes(): SizeDataCollection;

    /**
     * Get a collection of sizes available for this specific API credentials in a region provided.
     *
     * @param RegionDto|string $region RegionData object or a region slug.
     */
    public function getSizesAvailableInRegion(RegionDto|string $region): SizeDataCollection;

    /** Add a new SSH key to the provider. */
    public function addSshKey(string $name, string $publicKey): SshKeyDto;

    /** Get a collection of SSH keys added to this account. */
    public function getAllSshKeys(): SshKeyDataCollection;

    /** Add a new SSH key to the provider, checking if it was added before. */
    public function addSshKeySafely(string $name, string $publicKey): SshKeyDto;

    /**
     * Get an SSH key data by its identifier - ID or fingerprint.
     *
     * @param string $identifier Provider's ID or fingerprint.
     */
    public function getSshKey(string $identifier): SshKeyDto;

    /**
     * Change the name of an SSH key that was added previously.
     *
     * @param string $identifier Provider's ID or fingerprint.
     */
    public function updateSshKey(string $identifier, string $newName): SshKeyDto;
}
