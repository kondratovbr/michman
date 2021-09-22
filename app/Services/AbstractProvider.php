<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;

abstract class AbstractProvider
{
    private string $configPrefix;
    private string $basePath;
    protected string $cachePrefix;
    protected int|null $identifier;

    public function __construct(int $identifier = null)
    {
        $this->configPrefix = $this->getConfigPrefix();
        $this->basePath = $this->config('base_path');
        $this->identifier = $identifier;
    }

    /** Get an internal config name for this server provider. */
    abstract protected function getConfigPrefix(): string;

    /** Create a pending request with authentication configured. */
    abstract protected function request(): PendingRequest;

    /** Get a cache prefix for this specific server provider API credentials. */
    abstract protected function getCachePrefix(): string;

    /** Determine if we can even use cache to store some data for this API credentials. */
    protected function canUseCache(): bool
    {
        if (! isset($this->identifier)) {
            Log::warning(static::class . ': This instance is unable to cache data, because it doesn\'t have a cache identifier set for some reason.');
            return false;
        }

        return true;
    }

    /** Get a config value for this provider using standard dot-notation. */
    protected function config(string $key, mixed $default = null): mixed
    {
        return config($this->configPrefix . '.' . $key, $default);
    }

    /** Get a properly prefixed cache key for some parameter related to this specific server provider API credentials. */
    protected function cacheKey(string $key): string
    {
        return $this->getCachePrefix() . '.' . $key;
    }

    /** Decode JSON response throwing en exceptions on failure. */
    protected function decodeJson(string $json): array|object
    {
        return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    }

    /** Send a request to a relative path with provided parameters. */
    private function send(
        string $method,
        string $path,
        array $parameters = [],
        PendingRequest $pendingRequest = null,
    ): Response {
        $pendingRequest ??= $this->request();

        if (! in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']))
            throw new RuntimeException("Unsupported HTTP method \"{$method}\" provided.");

        return $pendingRequest
            ->send($method, $this->basePath . $path, $parameters)
            ->throw();
    }

    /** Send a GET request to a relative path with provided parameters. */
    protected function get(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        return $this->send('GET', $path, $parameters, $pendingRequest);
    }

    /** Send a POST request to a relative path with provided parameters. */
    protected function post(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        return $this->send('POST', $path, $parameters, $pendingRequest);
    }

    /** Send a PUT request to a relative path with provided parameters. */
    protected function put(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        return $this->send('PUT', $path, $parameters, $pendingRequest);
    }

    /** Send a PATCH request to a relative path with provided parameters. */
    protected function patch(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        return $this->send('PATCH', $path, $parameters, $pendingRequest);
    }

    /** Send a DELETE request to a relative path with provided parameters. */
    protected function delete(string $path, array $parameters = [], PendingRequest $pendingRequest = null): Response
    {
        return $this->send('DELETE', $path, $parameters, $pendingRequest);
    }
}
