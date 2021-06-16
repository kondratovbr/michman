<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use RuntimeException;

abstract class AbstractVcsProvider implements VcsProviderInterface
{
    private string $configKey;
    private string $basePath;
    private string $cachePrefix;
    protected int|null $identifier;

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
     * Get a config value for this provider using standard dot-notation.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return config("vcs.list.{$this->configKey}.{$key}", $default);
    }

    /**
     * Get a cache prefix for this specific server provider API credentials.
     */
    private function getCachePrefix(): string
    {
        // If we don't have an internal provider ID we cannot use cache at all -
        // we have nothing to use as a unique reproducible identifier.
        if (! isset($this->identifier))
            throw new RuntimeException('Cannot use caching for this VCS provider - no unique identifier provided.');

        if (! isset($this->cachePrefix))
            $this->cachePrefix = 'vcs.' . $this->identifier;

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
     * Get a properly prefixed cache key for some parameter related to this specific server provider API credentials.
     */
    protected function cacheKey(string $key): string
    {
        return $this->getCachePrefix() . '.' . $key;
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
}
