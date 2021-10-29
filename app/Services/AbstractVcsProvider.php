<?php declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use App\Support\Arr;
use phpseclib3\Crypt\PublicKeyLoader;
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
        $duplicatedAddedKey = $this->findDuplicatedKey($publicKey);

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

    private function findDuplicatedKey(string $publicKey): SshKeyDto|null
    {
        $publicKey = $this->cleanPublicKeyString($publicKey);

        $addedKeys = $this->getAllSshKeys();

        /** @var SshKeyDto $key */
        foreach ($addedKeys as $key) {
            if ($this->cleanPublicKeyString($key->publicKey) == $publicKey) {
                $duplicatedAddedKey = $key;
                break;
            }
        }

        return $duplicatedAddedKey ?? null;
    }

    /** Load public key from a string and re-format in a consistent way. */
    private function cleanPublicKeyString(string $publicKey): string
    {
        return PublicKeyLoader::load($publicKey)->toString('OpenSSH', ['comment' => '']);
    }

    public function getWebhookIfExistsPush(string $repo, string $payloadUrl): WebhookDto|null
    {
        $hooks = $this->getRepoWebhooks($repo);

        /** @var WebhookDto $hook */
        foreach ($hooks as $hook) {
            if ($hook->url === $payloadUrl && Arr::hasValue($hook->events, 'push'))
                return $hook;
        }

        return null;
    }

    public function addWebhookSafelyPush(string $repo, string $payloadUrl, string $secret): WebhookDto
    {
        $hook = $this->getWebhookIfExistsPush($repo, $payloadUrl);

        if (! is_null($hook))
            return $this->updateWebhookPush(
                $repo,
                $hook->id,
                $payloadUrl,
                $secret,
            );

        return $this->addWebhookPush($repo, $payloadUrl, $secret);
    }

    public function deleteWebhookIfExistsPush(string $repo, string $payloadUrl): void
    {
        $hook = $this->getWebhookIfExistsPush($repo, $payloadUrl);

        if (is_null($hook))
            return;

        $this->deleteWebhook($repo, $hook->id);
    }
}
