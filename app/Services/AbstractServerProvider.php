<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\SizeDataCollection;
use Illuminate\Support\Facades\Cache;

abstract class AbstractServerProvider extends AbstractProvider implements ServerProviderInterface
{
    /*
     * TODO: This is a type of cache that would be nice to keep maintained with a scheduled job.
     *       Otherwise some users will have to endure some long loadings.
     */

    /** @var int */
    private const REGIONS_CACHE_TTL = 60 * 60; // 1 hour
    /** @var int */
    private const SIZES_CACHE_TTL = 60 * 60; // 1 hour

    /*
     * These properties are used to cache some data internally just for
     * the lifecycle of this object.
     * This technique saves some cache requests.
     */
    private RegionDataCollection $allRegions;
    private SizeDataCollection $allSizes;

    /** Get a cache key for cache values that can be shared between providers. */
    abstract protected function commonCacheKey(string $key): string;

    /** Call the API for all server regions it supports. */
    abstract protected function getAllRegionsFromApi(): RegionDataCollection;

    /** Call the API for all server sizes it supports. */
    abstract protected function getAllSizesFromApi(): SizeDataCollection;

    /** Get a collection of all regions supported by the API using caching. */
    public function getAllRegions(): RegionDataCollection
    {
        if (! isset($this->allRegions))
            $this->allRegions = $this->retrieveAllRegions();

        return $this->allRegions;
    }

    /** Get a collection of all sizes supported by the API using caching. */
    public function getAllSizes(): SizeDataCollection
    {
        if (! isset($this->allSizes))
            $this->allSizes = $this->retrieveAllSizes();

        return $this->allSizes;
    }

    /**
     * Retrieve a collection of all regions supported by the API
     * either from cache or from the API.
     */
    private function retrieveAllRegions(): RegionDataCollection
    {
        return Cache::remember(
            $this->commonCacheKey('all-regions'),
            self::REGIONS_CACHE_TTL,
            fn() => $this->getAllRegionsFromApi()
        );
    }

    /**
     * Retrieve a collection of all sizes supported by the API
     * either from cache or from the API.
     */
    private function retrieveAllSizes(): SizeDataCollection
    {
        return Cache::remember(
            $this->commonCacheKey('all-sizes'),
            self::SIZES_CACHE_TTL,
            fn() => $this->getAllSizesFromApi()
        );
    }
}
