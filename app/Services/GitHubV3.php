<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\SshKeyDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

// TODO: CRITICAL! Should I handle possible redirects here? Does Laravel do it automatically?

// TODO: CRITICAL! Have I entirely forgot about pagination in responses?

/*
 * TODO: CRITICAL! I should also handle the "scope".
 *       I.e. if we don't have permission to perform some action we should notify the user and give them
 *       a button to repair permissions.
 */

// TODO: CRITICAL! Docs mention "304 Not Modified" responses. Do I have to explicitly cache the results somehow?

class GitHubV3 extends AbstractVcsProvider
{
    /**
     * Proper GitHub API v3 "Accept" header.
     *
     * @see https://docs.github.com/en/rest/overview/resources-in-the-rest-api#current-version
     */
    private const ACCEPT = 'application/vnd.github.v3+json';

    /** @var string Bearer token used for authentication. */
    private string $token;

    public function __construct(string $token, int $identifier = null)
    {
        parent::__construct($identifier);

        $this->token = $token;
    }

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token)->accept(static::ACCEPT);
    }

    protected function getConfigPrefix(): string
    {
        return 'vcs.list.github_v3';
    }

    public function credentialsAreValid(): bool
    {
        $response = $this->get('/user');

        return $response->successful();
    }

    public function getAllSshKeys(): SshKeyDataCollection
    {
        $response = $this->get('/user/keys');
        $data = $this->decodeJson($response->body());

        $collection = new SshKeyDataCollection;

        /** @var object $key */
        foreach ($data as $key) {
            $collection->push($this->sshKeyDataFromResponseData($key));
        }

        return $collection;
    }

    public function getSshKey(string $id): SshKeyDto
    {
        $response = $this->get("/user/keys/{$id}");
        $data = $this->decodeJson($response->body());

        return $this->sshKeyDataFromResponseData($data);
    }

    public function addSshKey(string $name, string $publicKey): SshKeyDto
    {
        $response = $this->post('/user/keys', [
            'title' => $name,
            'key' => $publicKey,
        ]);
        $data = $this->decodeJson($response->body());

        return $this->sshKeyDataFromResponseData($data);
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

    public function updateSshKey(SshKeyDto $sshKey): SshKeyDto
    {
        $this->deleteSshKey($sshKey->id);

        return $this->addSshKey($sshKey->name, $sshKey->publicKey);
    }

    public function deleteSshKey(string $id): void
    {
        $this->delete("/user/keys/{$id}");
    }

    public function getLatestCommitHash(string|null $fullRepoName, string $branch, string $username = null, string $repo = null): string
    {
        $fullRepoName ??= "{$username}/{$branch}";

        $response = $this->get("/repos/{$fullRepoName}/commits/{$branch}");
        $data = $this->decodeJson($response->body());

        return $data->sha;
    }

    /**
     * Convert SSH key object from response format to internal format.
     */
    protected function sshKeyDataFromResponseData(object $data): SshKeyDto
    {
        return new SshKeyDto(
            id: $data->id,
            publicKey: $data->key,
            name: $data->title,
        );
    }

    public function getSshHostKey(): string
    {
        return $this->config('ssh_host_key');
    }

    public static function getFullSshString(string $repo): string
    {
        return "git@github.com:{$repo}.git";
    }
}
