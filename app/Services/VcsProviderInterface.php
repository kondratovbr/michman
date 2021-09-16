<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;

/*
 * TODO: CRITICAL! Figure out how to avoid hitting the rate limit with these things. Probably move the actual HTTP logic into a singleton for each service, so it can track calls during a request and also between them by using cache. Or maybe just use cache every time?
 */

/*
 * TODO: CRITICAL! I also need DELETE functions for the APIs to remove stuff when users wants it
 *       and also to perform a cleanup when user deletes their entire account.
 */

// TODO: IMPORTANT! Figure out what to do with account statuses. I.e. a provider may lock the account if a payment failed or something. Have to handle it gracefully as well.

interface VcsProviderInterface
{
    /** Get a URL to the page with the specified commit on the VCS site. */
    public function commitUrl(string $repo, string $commit): string;

    /**
     * Check if provided credentials are valid by trying some auth-protected GET request.
     */
    public function credentialsAreValid(): bool;

    /**
     * Get a collection of SSH keys added to this account.
     */
    public function getAllSshKeys(): SshKeyDataCollection;

    /**
     * Get an SSH key data by its ID on the provider's side.
     *
     * @param string $id Provider's ID
     */
    public function getSshKey(string $id): SshKeyDto;

    /**
     * Add a new SSH key to the provider.
     */
    public function addSshKey(string $name, string $publicKey): SshKeyDto;

    /**
     * Add a new SSH key to the provider, checking if it was added before.
     */
    public function addSshKeySafely(string $name, string $publicKey): SshKeyDto;

    /*
     * TODO: CRITICAL! Make sure I update the corresponding model after this each time I do it -
     *       some providers don't allow to update keys, so the only way to implement this
     *       is to delete the key and add a new one, which will have a different ID.
     *       Check out other "update" method in ALL APIs as well.
     */
    /**
     * Update a previously added SSH key's name by its ID on the provider's side.
     *
     * @param string $id Provider's ID
     */
    public function updateSshKey(SshKeyDto $sshKey): SshKeyDto;

    /**
     * Delete a previously added SSH key by its ID on the provider's side.
     *
     * @param string $id
     */
    public function deleteSshKey(string $id): void;

    /**
     * Get a VCS provider server host key for SSH host verification.
     */
    public function getSshHostKey(): string;

    /**
     * Get the SHA hash of the latest commit on the specified branch.
     */
    public function getLatestCommitHash(string|null $fullRepoName, string $branch, string $username = null, string $repo = null): string;

    /**
     * Convert a short repo string, like "username/repo" to
     * a full SSH designation, like "git@github.com:username/repo.git".
     */
    public static function getFullSshString(string $repo): string;

    /**
     * Create a "push" webhook for a given repo.
     */
    public function addWebhookPush(string $repo, string $payloadUrl): WebhookDto;
}
