<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use RuntimeException;

abstract class AbstractVcsProvider extends AbstractProvider implements VcsProviderInterface
{
    protected function getCachePrefix(): string
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
     * Convert a short repo string, like "username/repo" to
     * a full SSH designation, like "git@github.com:username/repo.git".
     */
    abstract public static function getFullSshString(string $repo): string;
}
