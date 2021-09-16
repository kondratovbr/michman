<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\Collections\WebhookDataCollection;
use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use App\Services\Exceptions\ExternalApiException;
use App\Support\Arr;
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

    public function commitUrl(string $repo, string $commit): string
    {
        return "https://github.com/{$repo}/commit/{$commit}";
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

    public function getSshKey(string $sshKeyExternalId): SshKeyDto
    {
        $response = $this->get("/user/keys/{$sshKeyExternalId}");
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

    /** Convert SSH key object from response format to internal format. */
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

    public function getRepoWebhooks(string $repo): WebhookDataCollection
    {
        $response = $this->get("/repos/{$repo}/hooks");
        $data = $this->decodeJson($response->body());

        $collection = new WebhookDataCollection;

        /** @var object $hook */
        foreach ($data as $hook) {
            $collection->push($this->webhookDataFromResponseData($hook));
        }

        return $collection;
    }

    public function getWebhook(string $repo, string $webhookExternalId): WebhookDto
    {
        $response = $this->get("/repos/{$repo}/hooks/{$webhookExternalId}");
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    public function addWebhookPush(string $repo, string $payloadUrl): WebhookDto
    {
        $response = $this->post("/repos/{$repo}/hooks", [
            'config' => [
                'url' => $payloadUrl,
                // TODO: CRITICAL! Don't forget to change this to the actual route.
                // 'url' => 'https://www.michtest.com/',
                'content_type' => 'json',
                'insecure_ssl' => false,
                'events' => [
                    'push',
                ],
            ],
        ]);
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    public function addWebhookSafelyPush(string $repo, string $payloadUrl): WebhookDto
    {
        $hook = $this->getWebhookIfExistsPush($repo, $payloadUrl);

        if (! is_null($hook))
            return $hook;

        return $this->addWebhookPush($repo, $payloadUrl);
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

    public function deleteWebhook(string $repo, string $webhookExternalId): void
    {
        $this->delete("/repos/{$repo}/hooks/{$webhookExternalId}");
    }

    public function deleteWebhookIfExistsPush(string $repo, string $payloadUrl): void
    {
        $hook = $this->getWebhookIfExistsPush($repo, $payloadUrl);

        if (is_null($hook))
            return;

        $this->deleteWebhook($repo, $hook->id);
    }

    /** Convert webhook data from response format into the internal format. */
    protected function webhookDataFromResponseData(object $data): WebhookDto
    {
        return new WebhookDto(
            events: $data->events,
            id: (string) $data->id,
            url: $data->config->url,
        );
    }
}
