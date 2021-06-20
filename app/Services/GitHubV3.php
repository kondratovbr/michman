<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\SshKeyData;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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

    public function getSshKey(string $id): SshKeyData
    {
        $response = $this->get("/user/keys/{$id}");
        $data = $this->decodeJson($response->body());

        return $this->sshKeyDataFromResponseData($data);
    }

    public function addSshKey(string $name, string $publicKey): SshKeyData
    {
        $response = $this->post('/user/keys', [
            'title' => $name,
            'key' => $publicKey,
        ]);
        $data = $this->decodeJson($response->body());

        return $this->sshKeyDataFromResponseData($data);
    }

    public function addSshKeySafely(string $name, string $publicKey): SshKeyData
    {
        $addedKeys = $this->getAllSshKeys();

        /** @var SshKeyData $duplicatedAddedKey */
        $duplicatedAddedKey = $addedKeys->firstWhere('publicKey', $publicKey);

        if ($duplicatedAddedKey !== null) {
            if ($duplicatedAddedKey->name === $name)
                return $duplicatedAddedKey;

            return $this->updateSshKey(new SshKeyData(
                publicKey: $publicKey,
                name: $name,
            ));
        }

        return $this->addSshKey($name, $publicKey);
    }

    public function updateSshKey(SshKeyData $sshKey): SshKeyData
    {
        $this->deleteSshKey($sshKey->id);

        return $this->addSshKey($sshKey->name, $sshKey->publicKey);
    }

    public function deleteSshKey(string $id): void
    {
        $this->delete("/user/keys/{$id}");
    }

    /**
     * Convert SSH key object from response format to internal format.
     */
    protected function sshKeyDataFromResponseData(object $data): SshKeyData
    {
        return new SshKeyData(
            id: $data->id,
            publicKey: $data->key,
            name: $data->title,
        );
    }
}
