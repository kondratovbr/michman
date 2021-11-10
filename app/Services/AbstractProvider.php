<?php declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\AuthTokenDto;
use App\Services\Traits\HasConfig;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;

abstract class AbstractProvider implements ProviderInterface
{
    use HasConfig;

    protected AuthTokenDto $token;
    private string $basePath;

    public function __construct(AuthTokenDto $token)
    {
        $this->setConfigPrefix($this->getConfigPrefix());
        $this->basePath = $this->config('base_path');
        $this->token = $token;
    }

    /** Get an internal config name for this server provider. */
    abstract protected function getConfigPrefix(): string;

    /** Create a pending request with authentication configured. */
    abstract protected function request(): PendingRequest;

    /** Decode JSON response throwing en exceptions on failure. */
    protected function decodeJson(string $json): array|object
    {
        return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    }

    /** Create a pending request with caching configured. */
    private function requestWithCaching(): PendingRequest
    {
        return $this->request()->withMiddleware(new CacheMiddleware(
            new PrivateCacheStrategy(
                new LaravelCacheStorage(
                    Cache::store()
                )
            )
        ));
    }

    /** Get the url to the next page if provided. */
    protected function nextUrl(Response $response): string|null
    {
        return $response->nextUrl();
    }

    /**
     * Send a GET request to a relative path with provided parameters.
     *
     * Handles link-based pagination.
     *
     * @return Response|mixed
     */
    protected function get(string $path, array $query = [], callable $closure = null, mixed $initial = null): mixed
    {
        $response = $this->requestWithCaching()
            ->baseUrl($this->basePath)
            ->get($path, $query)
            ->throw();

        if (is_null($closure))
            return $response;

        $carry = $closure($initial, $this->decodeJson($response->body()));

        $next = $this->nextUrl($response);

        while (! is_null($next)) {
            $response = $this->requestWithCaching()->get($next)->throw();

            $carry = $closure($carry, $this->decodeJson($response->body()));

            $next = $this->nextUrl($response);
        }

        return $carry;
    }

    /** Send a POST request to a relative path with provided parameters. */
    protected function post(string $path, array $data = []): Response
    {
        $pendingRequest ??= $this->request();

        return $pendingRequest
            ->baseUrl($this->basePath)
            ->post($path, $data)
            ->throw();
    }

    /** Send a PUT request to a relative path with provided parameters. */
    protected function put(string $path, array $data = [], PendingRequest $pendingRequest = null): Response
    {
        $pendingRequest ??= $this->request();

        return $pendingRequest
            ->baseUrl($this->basePath)
            ->put($path, $data)
            ->throw();
    }

    /** Send a PATCH request to a relative path with provided parameters. */
    protected function patch(string $path, array $data = [], PendingRequest $pendingRequest = null): Response
    {
        $pendingRequest ??= $this->request();

        return $pendingRequest
            ->baseUrl($this->basePath)
            ->patch($path, $data)
            ->throw();
    }

    /** Send a DELETE request to a relative path with provided parameters. */
    protected function delete(string $path, array $data = [], PendingRequest $pendingRequest = null): Response
    {
        $pendingRequest ??= $this->request();

        return $pendingRequest
            ->baseUrl($this->basePath)
            ->delete($path, $data)
            ->throw();
    }
}
