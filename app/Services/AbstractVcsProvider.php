<?php declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\SshKeyDto;
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

    public function addSshKeySafely(string $name, string $publicKey): SshKeyDto
    {
        $addedKeys = $this->getAllSshKeys();

        /** @var SshKeyDto $duplicatedAddedKey */
        $duplicatedAddedKey = $addedKeys->firstWhere('publicKey', $publicKey);

        if ($duplicatedAddedKey !== null) {
            if ($duplicatedAddedKey->name === $name)
                return $duplicatedAddedKey;

            return $this->updateSshKey(new SshKeyDto(
                id: $duplicatedAddedKey->id,
                publicKey: $publicKey,
                name: $name,
            ));
        }

        return $this->addSshKey($name, $publicKey);
    }
}
