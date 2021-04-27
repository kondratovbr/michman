<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionCollection;
use App\Collections\SizeCollection;
use App\Collections\SshKeyCollection;
use App\DataTransferObjects\RegionData;
use App\DataTransferObjects\ServerData;
use App\DataTransferObjects\SizeData;
use App\DataTransferObjects\SshKeyData;
use App\Support\Arr;

// TODO: IMPORTANT! Cover the thing with tests.

// TODO: CRITICAL! We should somehow gracefully fail if the API returns something unexpected or doesn't respond at all.

// TODO: CRITICAL! Have I entirely forgot about pagination in responses?

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
        $response = $this->getJson('/sizes');
        $data = json_decode($response->body());

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
        $data = json_decode($response->body());

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

    public function addSshKey(string $name, string $publicKey): string
    {
        $response = $this->postJson('/account/keys', [
            'name' => $name,
            'public_key' => $publicKey,
        ]);

        return (string) json_decode($response->body())->ssh_key->id;
    }

    public function getAllSshKeys(): SshKeyCollection
    {
        $response = $this->getJson('/account/keys');
        $data = json_decode($response->body());

        $collection = new SshKeyCollection;

        /** @var object $key */
        foreach ($data->ssh_keys as $key) {
            $collection->push(new SshKeyData(
                id: $key->id,
                fingerptint: $key->fingerprint,
                publicKey: $key->public_key,
                name: $key->name,
            ));
        }

        return $collection;
    }
}
