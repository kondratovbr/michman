<?php declare(strict_types=1);

namespace App\Services\Traits;

trait HasConfig
{
    private string $configPrefix;

    /** Set a prefix to be used to retrieve config values for this entity. */
    protected function setConfigPrefix(string $prefix): void
    {
        $this->configPrefix = $prefix;
    }

    /** Get a config value for this entity using standard dot-notation. */
    protected function config(string $key, mixed $default = null): mixed
    {
        return config($this->configPrefix . '.' . $key, $default);
    }
}
