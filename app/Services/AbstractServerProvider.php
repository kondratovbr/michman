<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\SizeDataCollection;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

// TODO: Maybe refactor this so the app itself only uses methods from the Provider model,
//       so the model would actually be able to use caching and such.

abstract class AbstractServerProvider implements ServerProviderInterface
{
    /** @var int */
    private const REGIONS_CACHE_TTL = 60 * 60; // 1 hour
    /** @var int */
    private const SIZES_CACHE_TTL = 60 * 60; // 1 hour

    private string $configKey;
    private string $basePath;
    private string $cachePrefix;
    protected int|null $identifier;

    /*
     * These properties are used to cache some data internally just for
     * the lifecycle of this object.
     * This technique saves some external API requests.
     */
    private RegionDataCollection $allRegions;
    private SizeDataCollection $allSizes;

    public function __construct(int $identifier = null)
    {
        $this->configKey = $this->getConfigKey();
        $this->basePath = $this->config('base_path');
        $this->identifier = $identifier;
    }

    /**
     * Get an internal config name for this server provider.
     */
    abstract protected function getConfigKey(): string;

    /**
     * Create a pending request with authentication configured.
     */
    abstract protected function request(): PendingRequest;

    /**
     * Call the API for all server regions it supports.
     */
    abstract protected function getAllRegionsFromApi(): RegionDataCollection;

    /**
     * Call the API for all server sizes it supports.
     */
    abstract protected function getAllSizesFromApi(): SizeDataCollection;

    /**
     * Get a cache prefix for this specific server provider API credentials.
     */
    private function getCachePrefix(): string
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
     * Determine if we can even use cache to store some data for this API credentials.
     */
    private function canUseCache(): bool
    {
        return isset($this->identifier);
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
     * Send a GET request to a relative path with provided parameters.
     */
    protected function get(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        if (! isset($pendingRequest))
            $pendingRequest = $this->request();

        return $pendingRequest
            ->get($this->basePath . $path, $parameters)
            ->throw();
    }

    /**
     * Send a GET request to a relative path with provided parameters
     * and explicitly indicate to expect a JSON response
     * by attaching a "content-type: application/json" HTTP header.
     */
    protected function getJson(string $path, array $parameters = []): Response
    {
        return $this->get($path, $parameters, $this->request()->acceptJson());
    }

    /**
     * Send a POST request to a relative path with provided parameters.
     */
    protected function post(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        if (! isset($pendingRequest))
            $pendingRequest = $this->request();

        return $pendingRequest
            ->post($this->basePath . $path, $parameters)
            ->throw();
    }

    /**
     * Send a POST request to a relative path with provided parameters
     * and explicitly indicate to expect a JSON response
     * by attaching a "content-type: application/json" HTTP header.
     */
    protected function postJson(string $path, array $parameters = []): Response
    {
        return $this->post($path, $parameters, $this->request()->acceptJson());
    }

    /**
     * Send a PUT request to a relative path with provided parameters.
     */
    protected function put(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        if (! isset($pendingRequest))
            $pendingRequest = $this->request();

        return $pendingRequest
            ->put($this->basePath . $path, $parameters)
            ->throw();
    }

    /**
     * Send a PUT request to a relative path with provided parameters
     * and explicitly indicate to expect a JSON response
     * by attaching a "content-type: application/json" HTTP header.
     */
    protected function putJson(string $path, array $parameters = []): Response
    {
        return $this->put($path, $parameters, $this->request()->acceptJson());
    }

    /**
     * Send a DELETE request to a relative path with provided parameters.
     */
    protected function delete(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        if (! isset($pendingRequest))
            $pendingRequest = $this->request();

        return $pendingRequest
            ->delete($this->basePath . $path, $parameters)
            ->throw();
    }

    /**
     * Send a DELETE request to a relative path with provided parameters
     * and explicitly indicate to expect a JSON response
     * by attaching a "content-type: application/json" HTTP header.
     */
    protected function deleteJson(string $path, array $parameters = []): Response
    {
        return $this->delete($path, $parameters, $this->request()->acceptJson());
    }

    /**
     * Get a config value for this provider using standard dot-notation.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return config('providers.list.' . $this->configKey . '.' . $key, $default);
    }

    /**
     * Get a properly prefixed cache key for some parameter related to this specific server provider API credentials.
     */
    protected function cacheKey(string $key): string
    {
        return $this->getCachePrefix() . '.' . $key;
    }

    /**
     * Decode JSON response throwing en exceptions on failure.
     */
    protected function decodeJson(string $json): object
    {
        return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
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
