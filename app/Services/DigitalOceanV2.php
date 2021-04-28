<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionCollection;
use App\Collections\SizeCollection;
use App\Collections\SshKeyCollection;
use App\DataTransferObjects\NewServerData;
use App\DataTransferObjects\RegionData;
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

    public function createServer(ServerData $data): string
    {
        // TODO: Implement createServer() method.
    }

    protected function getAllSizesFromApi(): SizeCollection
    {
        $response = $this->getJson('/sizes')->json();
        $data = $this->decodeJson($response->body());

        $collection = new SizeCollection;

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

    protected function getAllRegionsFromApi(): RegionCollection
    {
        $response = $this->getJson('/regions');
        $data = $this->decodeJson(($response->body()));

        $collection = new RegionCollection;

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

    public function getAvailableRegions(): RegionCollection
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

    public function getAvailableSizes(): SizeCollection
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

    public function getSizesAvailableInRegion(RegionData|string $region): SizeCollection
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

        return $this->sshKeyDataFromResponseObject($data->ssh_key);
    }

    public function getAllSshKeys(): SshKeyCollection
    {
        $response = $this->getJson('/account/keys');
        $data = $this->decodeJson(($response->body()));

        $collection = new SshKeyCollection;

        /** @var object $key */
        foreach ($data->ssh_keys as $key) {
            $collection->push($this->sshKeyDataFromResponseObject($key));
        }

        return $collection;
    }

    public function getSshKey(string $identifier): SshKeyData
    {
        $response = $this->getJson('/account/keys/' . $identifier);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseObject($data->ssh_key);
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

        return $this->sshKeyDataFromResponseObject($data->ssh_key);
    }

    /**
     * Convert SSH key response object format to internal format.
     */
    protected function sshKeyDataFromResponseObject(object $data): SshKeyData
    {
        return new SshKeyData(
            id: $data->id,
            fingerprint: $data->fingerprint,
            publicKey: $data->public_key,
            name: $data->name,
        );
    }
}
