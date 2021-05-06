<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\ServerDataCollection;
use App\Collections\SizeDataCollection;
use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\NewServerData;
use App\DataTransferObjects\RegionData;
use App\DataTransferObjects\ServerData;
use App\DataTransferObjects\SizeData;
use App\DataTransferObjects\SshKeyData;
use App\Services\Exceptions\ExternalApiException;
use App\Support\Arr;

// TODO: IMPORTANT! Cover the thing with tests.

// TODO: CRITICAL! We should somehow gracefully fail if the API returns something unexpected or doesn't respond at all.

// TODO: CRITICAL! Have I entirely forgot about pagination in responses?

// TODO: IMPORTANT! Should I add some reasonable timeouts here?

class DigitalOceanV2 extends AbstractServerProvider
{
    use UsesBearerTokens;

    /** @var string Bearer token used for authentication. */
    private string $token;

    public function __construct(string $token, int $identifier = null)
    {
        parent::__construct($identifier);

        $this->token = $token;
    }

    protected function getToken(): string
    {
        return $this->token;
    }

    protected function getConfigKey(): string
    {
        return 'digital_ocean_v2';
    }

    public function credentialsAreValid(): bool
    {
        $response = $this->getJson('/account');

        return $response->successful();
    }

    protected function getAllSizesFromApi(): SizeDataCollection
    {
        $response = $this->getJson('/sizes');
        $data = $this->decodeJson($response->body());

        $collection = new SizeDataCollection;

        /** @var object $size */
        foreach ($data->sizes as $size) {
            $collection->push(new SizeData(
                slug: $size->slug,
                transfer: $size->transfer,
                priceMonthly: $size->price_monthly,
                memoryMb: $size->memory,
                cpus: $size->vcpus,
                diskGb: $size->disk,
                regions: $size->regions,
                available: $size->available,
                description: $size->description,
            ));
        }

        return $collection;
    }

    protected function getAllRegionsFromApi(): RegionDataCollection
    {
        $response = $this->getJson('/regions');
        $data = $this->decodeJson(($response->body()));

        $collection = new RegionDataCollection;

        /** @var object $region */
        foreach ($data->regions as $region) {
            $collection->push(new RegionData(
                name: $region->name,
                slug: $region->slug,
                sizes: $region->sizes,
                available: $region->available,
            ));
        }

        return $collection;
    }

    public function getAvailableRegions(): RegionDataCollection
    {
        $availableSizes = $this->getAllSizes()
            ->filter(fn(SizeData $size) => $size->available)
            ->pluck('slug');

        return $this->getAllRegions()->filter(fn(RegionData $region) =>
            $region->available
            && ! Arr::empty($region->sizes)
            && $availableSizes->intersect($region->sizes)->isNotEmpty()
        );
    }

    public function getAvailableSizes(): SizeDataCollection
    {
        $availableRegions = $this->getAllRegions()
            ->filter(fn(RegionData $region) => $region->available)
            ->pluck('slug');

        return $this->getAllSizes()->filter(fn(SizeData $size) =>
            $size->available
            && ! Arr::empty($size->regions)
            && $availableRegions->intersect($size->regions)->isNotEmpty()
        );
    }

    public function getSizesAvailableInRegion(RegionData|string $region): SizeDataCollection
    {
        return $this->getAvailableSizes()->filter(fn(SizeData $size) =>
            Arr::hasValue($size->regions, is_string($region) ? $region : $region->slug)
        );
    }

    public function addSshKey(string $name, string $publicKey): SshKeyData
    {
        $response = $this->postJson('/account/keys', [
            'name' => $name,
            'public_key' => $publicKey,
        ]);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function getAllSshKeys(): SshKeyDataCollection
    {
        $response = $this->getJson('/account/keys');
        $data = $this->decodeJson(($response->body()));

        $collection = new SshKeyDataCollection;

        /** @var object $key */
        foreach ($data->ssh_keys as $key) {
            $collection->push($this->sshKeyDataFromResponseData($key));
        }

        return $collection;
    }

    public function getSshKey(string $identifier): SshKeyData
    {
        $response = $this->getJson('/account/keys/' . $identifier);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function addSshKeySafely(string $name, string $publicKey): SshKeyData
    {
        $addedKeys = $this->getAllSshKeys();

        /** @var SshKeyData $duplicatedAddedKey */
        $duplicatedAddedKey = $addedKeys->firstWhere('publicKey', $publicKey);

        if ($duplicatedAddedKey !== null) {
            if ($duplicatedAddedKey->name === $name)
                return $duplicatedAddedKey;

            return $this->updateSshKey($duplicatedAddedKey->id, $name);
        }

        return $this->addSshKey($name, $publicKey);
    }

    public function updateSshKey(string $identifier, string $newName): SshKeyData
    {
        $response = $this->putJson('/account/keys/' . $identifier, [
            'name' => $newName,
        ]);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function createServer(NewServerData $data, string $sshKeyIdentifier): ServerData
    {
        if (empty($sshKeyIdentifier))
            throw new ExternalApiException('No SSH key identifier provided. SSH key is required to request a new server, it should be added to the server provider account beforehand.');

        $response = $this->postJson('/droplets', [
            'name' => $data->name,
            'region' => $data->region,
            'size' => $data->size,
            'image' => (string) config('providers.list.digital_ocean_v2.default_image'),
            'ssh_keys' => [$sshKeyIdentifier],
            'monitoring' => true,
        ]);
        $data = $this->decodeJson($response->body());

        return $this->serverDataFromResponseData($data->droplet);
    }

    public function getServer(string $serverId): ServerData
    {
        $response = $this->getJson('/droplets/' . $serverId);
        $data = $this->decodeJson($response->body());

        return $this->serverDataFromResponseData($data->droplet);
    }

    public function getAllServers(): ServerDataCollection
    {
        $response = $this->getJson('/droplets');
        $data = $this->decodeJson($response->body());

        $servers = new ServerDataCollection;

        foreach ($data->droplets as $droplet) {
            $servers->push($this->serverDataFromResponseData($droplet));
        }

        return $servers;
    }

    public function getServerPublicIp4(string $serverId): string|null
    {
        $response = $this->getJson('/droplets/' . $serverId);
        $data = $this->decodeJson($response->body());

        return $this->publicIpFromNetworks($data->droplet->networks->v4);
    }

    /**
     * Convert SSH key object from response format to internal format.
     */
    protected function sshKeyDataFromResponseData(object $data): SshKeyData
    {
        return new SshKeyData(
            id: $data->id,
            fingerprint: $data->fingerprint,
            publicKey: $data->public_key,
            name: $data->name,
        );
    }

    /**
     * Convert server object from response format to internal format.
     */
    protected function serverDataFromResponseData(object $data): ServerData
    {
        return new ServerData(
            id: $data->id,
            name: $data->name,
            publicIp4: $this->publicIpFromNetworks($data->networks->v4 ?? []),
        );
    }

    /**
     * Extract a public IP address from a list of networks attached to a server.
     */
    protected function publicIpFromNetworks(array $networks): string|null
    {
        foreach ($networks as $network) {
            if ($network->type === 'public')
                return $network->ip_address;
        }

        return null;
    }
}
