<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionCollection;
use App\Collections\SizeCollection;
use App\DataTransferObjects\RegionData;
use App\DataTransferObjects\ServerData;
use App\DataTransferObjects\SizeData;
use App\Support\Arr;

// TODO: CRITICAL. We should somehow gracefully fail if the API returns something unexpected or doesn't respond at all.

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
                memoryMb: $size->memory,
                cpus: $size->vcpus,
                diskGb: $size->disk,
                regions: $size->regions,
                available: $size->available,
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

    public function getSizesAvailableInRegion(RegionData $region): SizeCollection
    {
        return $this->getAvailableSizes()->filter(fn(SizeData $size) =>
            Arr::hasValue($size->regions, $region->slug)
        );
    }
}
