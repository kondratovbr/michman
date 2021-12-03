<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\Collections\WebhookDataCollection;
use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use App\Support\Arr;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/*
 * TODO: IMPORTANT! I should also handle the "scope". A user can change permissions given to us in the GitHub UI.
 *       I.e. if we don't have permission to perform some action we should notify the user and give them
 *       a button to repair permissions.
 */

// TODO: CRITICAL! I had a problem with GitHub credentials - should verify that the tokens stay valid over long periods of time. Like, login to the app and then leave it be for a couple of days and try accessing GitHub with the stored credentials.

class GitHubV3 extends AbstractVcsProvider
{
    /**
     * Proper GitHub API v3 "Accept" header.
     *
     * @see https://docs.github.com/en/rest/overview/resources-in-the-rest-api#current-version
     */
    private const ACCEPT = 'application/vnd.github.v3+json';

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token->token)->accept(static::ACCEPT);
    }

    protected function getConfigPrefix(): string
    {
        return 'vcs.list.github_v3';
    }

    public function commitUrl(string $repo, string $commit): string
    {
        return "https://github.com/{$repo}/commit/{$commit}";
    }

    public function repoUrl(string $repo): string
    {
        return "https://github.com/{$repo}";
    }

    public function credentialsAreValid(): bool
    {
        try {
            $response = $this->get('/user');
        } catch (RequestException $e) {
            return false;
        }

        return $response->successful();
    }

    public function getAllSshKeys(): SshKeyDataCollection
    {
        return $this->get('/user/keys', [],
            function (SshKeyDataCollection $carry, array $data) {
                /** @var object $key */
                foreach ($data as $key) {
                    $carry->push($this->sshKeyDataFromResponseData($key));
                }

                return $carry;
            },
        new SshKeyDataCollection);
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

    public function updateSshKey(SshKeyDto $sshKey): SshKeyDto
    {
        $this->deleteSshKey($sshKey->id);

        return $this->addSshKey($sshKey->name, $sshKey->publicKey);
    }

    public function deleteSshKey(string $id): void
    {
        $this->delete("/user/keys/{$id}");
    }

    /** https://docs.github.com/en/rest/reference/repos#get-a-commit */
    public function getLatestCommitHash(
        string|null $fullRepoName,
        string $branch,
        string $username = null,
        string $repo = null,
    ): string {
        $fullRepoName ??= "{$username}/{$repo}";

        $response = $this->get("/repos/{$fullRepoName}/commits/{$branch}");
        $data = $this->decodeJson($response->body());

        return $data->sha;
    }

    /** Convert SSH key object from response format to internal format. */
    protected function sshKeyDataFromResponseData(object $data): SshKeyDto
    {
        return new SshKeyDto(
            id: (string) $data->id,
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

    /** https://docs.github.com/en/rest/reference/repos#list-repository-webhooks */
    public function getRepoWebhooks(string $repo): WebhookDataCollection
    {
        return $this->get("/repos/{$repo}/hooks", [],
            function (WebhookDataCollection $carry, array $data) {
                /** @var object $key */
                foreach ($data as $key) {
                    $carry->push($this->webhookDataFromResponseData($key));
                }

                return $carry;
            },
            new WebhookDataCollection);
    }

    /** https://docs.github.com/en/rest/reference/repos#get-a-repository-webhook */
    public function getWebhook(string $repo, string $webhookExternalId): WebhookDto
    {
        $response = $this->get("/repos/{$repo}/hooks/{$webhookExternalId}");
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.github.com/en/rest/reference/repos#create-a-repository-webhook */
    public function addWebhookPush(string $repo, string $payloadUrl, string $secret): WebhookDto
    {
        $response = $this->post("/repos/{$repo}/hooks", [
            'config' => $this->webhookConfigArray('push', $payloadUrl, $secret),
        ]);
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.github.com/en/rest/reference/repos#update-a-repository-webhook */
    public function updateWebhookPush(
        string $repo,
        string $webhookExternalId,
        string $payloadUrl,
        string $secret,
    ): WebhookDto {
        $response = $this->patch("/repos/{$repo}/hooks/{$webhookExternalId}", [
            'config' => $this->webhookConfigArray('push', $payloadUrl, $secret),
        ]);
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.github.com/en/rest/reference/repos#delete-a-repository-webhook */
    public function deleteWebhook(string $repo, string $webhookExternalId): void
    {
        $this->delete("/repos/{$repo}/hooks/{$webhookExternalId}");
    }

    public function dispatchesPingWebhookCalls(): bool
    {
        return true;
    }

    public function refreshToken(): AuthTokenDto
    {
        Log::warning("GitHubV3::refreshToken() method was called, but GitHub's tokens don't expire, so it shouldn't have been called at all.");

        return $this->token;
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

    protected function webhookConfigArray(
        array|string $events,
        string $payloadUrl,
        string $secret,
    ): array {
        return [
            'url' => $payloadUrl,
            'content_type' => 'json',
            'insecure_ssl' => false,
            'secret' => $secret,
            'events' => Arr::wrap($events),
        ];
    }
}
