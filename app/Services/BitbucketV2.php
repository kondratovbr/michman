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
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// TODO: IMPORTANT! Cover with tests.

// TODO: CRITICAL! CONTINUE.
// TODO: CRITICAL! CONTINUE. Now, implement BitbucketWebhookService class and test webhooks and deployment from Bitbucket.

class BitbucketV2 extends AbstractVcsProvider
{
    private string $userId;

    protected function getConfigPrefix(): string
    {
        return 'vcs.list.bitbucket_v2';
    }

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token->token);
    }

    /** Override the standard nextUrl method - Bitbucket returns links in the body instead of a header. */
    protected function nextUrl(Response $response): string|null
    {
        return $this->decodeJson($response->body())->next ?? null;
    }

    public function refreshToken(): AuthTokenDto
    {
        if (empty($this->token->refresh_token))
            throw new RuntimeException('No refresh token provided in the token data.');

        $response = Http::withBasicAuth(
            config('services.bitbucket.client_id'),
            config('services.bitbucket.client_secret')
        )
            ->asForm()
            ->post('https://bitbucket.org/site/oauth2/access_token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->token->refresh_token,
            ])
            ->throw();

        $data = $this->decodeJson($response->body());

        return AuthTokenDto::fromData(
            null,
            $data->access_token,
            $data->refresh_token,
            $data->expires_in,
        );
    }

    public function commitUrl(string $repo, string $commit): string
    {
        return "https://bitbucket.org/{$repo}/commits/{$commit}";
    }

    public function repoUrl(string $repo): string
    {
        return "https://bitbucket.org/{$repo}";
    }

    public function credentialsAreValid(): bool
    {
        try {
            $response = $this->get('/user');
        } catch (RequestException) {
            return false;
        }

        return $response->successful();
    }

    /**
     * Get the Bitbucket account ID for the current user.
     * Some Bitbucket endpoints require user ID.
     */
    private function userId(): string
    {
        if (! isset($this->userId)) {
            $this->userId = $this->decodeJson($this->get('/user')->body())->account_id;
        }

        return $this->userId;
    }

    public function getAllSshKeys(): SshKeyDataCollection
    {
        return $this->get("/users/{$this->userId()}/ssh-keys", [],
            function (SshKeyDataCollection $carry, object $data) {
                /** @var object $key */
                foreach ($data->values as $key) {
                    $carry->push($this->sshKeyDataFromResponseData($key));
                }

                return $carry;
            },
            new SshKeyDataCollection,
        );
    }

    public function getSshKey(string $sshKeyExternalId): SshKeyDto
    {
        $response = $this->get("/users/{$this->userId()}/ssh-keys/{$sshKeyExternalId}");
        $data = $this->decodeJson($response->body());

        return $this->sshKeyDataFromResponseData($data);
    }

    public function addSshKey(string $name, string $publicKey): SshKeyDto
    {
        throw new RuntimeException('Bitbucket does not support adding SSH keys via API.');
    }

    public function updateSshKey(SshKeyDto $sshKey): SshKeyDto
    {
        throw new RuntimeException('Bitbucket does not support updating SSH keys via API.');
    }

    public function deleteSshKey(string $id): void
    {
        throw new RuntimeException('Bitbucket does not support deleting SSH keys via API.');
    }

    public function getSshHostKey(): string
    {
        return $this->config('ssh_host_key');
    }

    public function getLatestCommitHash(
        string|null $fullRepoName,
        string $branch,
        string $username = null,
        string $repo = null,
    ): string {
        $fullRepoName ??= "{$username}/{$repo}";

        $response = $this->get("/repositories/{$fullRepoName}/commits/{$branch}");
        $data = $this->decodeJson($response->body());

        return $data->values[0]->hash;
    }

    public static function getFullSshString(string $repo): string
    {
        return "git@bitbucket.org:{$repo}.git";
    }

    /** https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-hooks-uid-get */
    public function getWebhook(string $repo, string $webhookExternalId): WebhookDto
    {
        // TODO: CRITICAL! Test this.

        $response = $this->get("/repositories/{$repo}/hooks/{$webhookExternalId}");
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-hooks-get */
    public function getRepoWebhooks(string $repo): WebhookDataCollection
    {
        // TODO: CRITICAL! Test this.

        return $this->get("/repositories/{$repo}/hooks", [],
            function (WebhookDataCollection $carry, object $data) {
                /** @var object $key */
                foreach ($data->values as $key) {
                    $carry->push($this->webhookDataFromResponseData($key));
                }

                return $carry;
            },
            new WebhookDataCollection,
        );
    }

    /** https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-hooks-post */
    public function addWebhookPush(string $repo, string $payloadUrl, string $secret): WebhookDto
    {
        // TODO: CRITICAL! Test this. Bitbucket doesn't use secrets? So, what's with these methods then?

        $response = $this->post("/repositories/{$repo}/hooks",
            $this->pushWebhookRequestData($payloadUrl),
        );
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-hooks-uid-put */
    public function updateWebhookPush(string $repo, string $webhookExternalId, string $payloadUrl, string $secret): WebhookDto
    {
        // TODO: CRITICAL! Test this.

        $response = $this->put("/repositories/{$repo}/hooks/{$webhookExternalId}",
            $this->pushWebhookRequestData($payloadUrl),
        );
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-hooks-uid-delete */
    public function deleteWebhook(string $repo, string $webhookExternalId): void
    {
        // TODO: CRITICAL! Test this.

        $this->delete("/repositories/{$repo}/hooks/{$webhookExternalId}");
    }

    public function dispatchesPingWebhookCalls(): bool
    {
        return false;
    }

    /** Convert SSH key object from response format to internal format. */
    protected function sshKeyDataFromResponseData(object $data): SshKeyDto
    {
        return new SshKeyDto(
            id: (string) $data->uuid,
            publicKey: $data->key,
            name: $data->label,
        );
    }

    /** Convert webhook data from response format into the internal format. */
    protected function webhookDataFromResponseData(object $data): WebhookDto
    {
        return new WebhookDto(
            events: $this->eventsArrayFromData($data),
            id: (string) $data->uuid,
            url: $data->url,
        );
    }

    /** Convert webhook data from a response to an array of events. */
    protected function eventsArrayFromData(object $data): array
    {
        // https://support.atlassian.com/bitbucket-cloud/docs/event-payloads/
        $events = [
            'repo:push' => 'push',
            //
        ];

        $result = [];

        foreach ($events as $bitbucketEvent => $appEvent) {
            if (Arr::hasValue($data->events, $bitbucketEvent))
                $result[] = $appEvent;
        }

        return $result;
    }

    /** Get a request data array for creating or updating a push webhook. */
    protected function pushWebhookRequestData(string $payloadUrl): array
    {
        return [
            'description' => 'Michman Auto Deploy Webhook',
            'url' => $payloadUrl,
            'active' => true,
            'events' => ['repo:push'],
        ];
    }
}
