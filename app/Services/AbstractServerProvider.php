<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

abstract class AbstractServerProvider implements ServerProviderInterface
{
    protected string $configKey;
    protected string $basePath;

    public function __construct()
    {
        $this->configKey = $this->getConfigKey();
        $this->basePath = $this->config('base_path');
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
     * Send a GET request to a relative path with provided parameters.
     */
    protected function get(string $path, array $parameters = []): Response
    {
        return $this->request()->get($this->basePath . $path, $parameters);
    }

    /**
     * Get a config value for this provider using standard dot-notation.
     */
    protected function config(string $key): mixed
    {
        return config('providers.list.' . $this->configKey . '.' . $key);
    }
}
