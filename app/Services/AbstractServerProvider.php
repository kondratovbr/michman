<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\SizeDataCollection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

// TODO: Maybe refactor this so the app itself only uses methods from the Provider model,
//       so the model would actually be able to use caching and such.

abstract class AbstractServerProvider extends AbstractProvider implements ServerProviderInterface
{
    /** @var int */
    private const REGIONS_CACHE_TTL = 60 * 60; // 1 hour
    /** @var int */
    private const SIZES_CACHE_TTL = 60 * 60; // 1 hour

    /*
     * These properties are used to cache some data internally just for
     * the lifecycle of this object.
     * This technique saves some external API requests.
     */
    private RegionDataCollection $allRegions;
    private SizeDataCollection $allSizes;

    /**
     * Call the API for all server regions it supports.
     */
    abstract protected function getAllRegionsFromApi(): RegionDataCollection;

    /**
     * Call the API for all server sizes it supports.
     */
    abstract protected function getAllSizesFromApi(): SizeDataCollection;

    protected function getCachePrefix(): string
    {
        // If we don't have an internal provider ID we cannot use cache at all -
        // we have nothing to use as a unique reproducible identifier.
        if (! isset($this->identifier))
            throw new RuntimeException('Cannot use caching for this provider - no unique identifier provided.');

        if (! isset($this->cachePrefix))
            $this->cachePrefix = 'provider.' . $this->identifier;

        return $this->cachePrefix;
    }

    /**
     * Retrieve a collection of all regions supported by the API
     * either from cache or from the API.
     */
    private function retrieveAllRegions(): RegionDataCollection
    {
        if (! $this->canUseCache())
            return $this->getAllRegionsFromApi();

        return Cache::remember(
            $this->cacheKey('all-regions'),
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
        if (! $this->canUseCache())
            return $this->getAllSizesFromApi();

        return Cache::remember(
            $this->cacheKey('all-sizes'),
            self::SIZES_CACHE_TTL,
            fn() => $this->getAllSizesFromApi()
        );
    }

    /**
     * Get a collection of all regions supported by the API using caching.
     */
    public function getAllRegions(): RegionDataCollection
    {
        if (! isset($this->allRegions))
            $this->allRegions = $this->retrieveAllRegions();

        return $this->allRegions;
    }

    /**
     * Get a collection of all sizes supported by the API using caching.
     */
    public function getAllSizes(): SizeDataCollection
    {
        if (! isset($this->allSizes))
            $this->allSizes = $this->retrieveAllSizes();

        return $this->allSizes;
    }
}
