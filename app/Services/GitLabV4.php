<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\Collections\WebhookDataCollection;
use App\DataTransferObjects\OAuthTokenDto;
use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

// TODO: CRITICAL! CONTINUE. Test this. Create a repo on GitLab and try to deploy a project from it.
// TODO: CRITICAL! Cover with tests. Don't forget to cover stuff that I moved to AbstractVcsProvider - it should be tested for every API.

/*
 * TODO: IMPORTANT! I should also handle the "scope".
 *       I.e. if we don't have permission to perform some action we should notify the user and give them
 *       a button to repair permissions.
 */

class GitLabV4 extends AbstractVcsProvider
{
    /** @var string Bearer token used for authentication. */
    private string $token;

    public function __construct(string $token, int $identifier = null)
    {
        parent::__construct($identifier);

        $this->token = $token;
    }

    protected function getConfigPrefix(): string
    {
        return 'vcs.list.gitlab_v4';
    }

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token);
    }

    public function commitUrl(string $repo, string $commit): string
    {
        // TODO: CRITICAL! Test this.

        return "https://gitlab.com/{$repo}/-/commit/{$commit}";
    }

    public function credentialsAreValid(): bool
    {
        $response = $this->get('/user');

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

    public function getSshHostKey(): string
    {
        return $this->config('ssh_host_key');
    }

    public function getLatestCommitHash(string|null $fullRepoName, string $branch, string $username = null, string $repo = null): string
    {
        // TODO: CRITICAL! UNFINISHED AND UNTESTED.

        $fullRepoName ??= "{$username}/{$branch}";

        $response = $this->get("/repos/{$fullRepoName}/commits/{$branch}");
        $data = $this->decodeJson($response->body());

        return $data->sha;
    }

    public static function getFullSshString(string $repo): string
    {
        return "git@gitlab.com:{$repo}.git";
    }

    /** https://docs.gitlab.com/ee/api/projects.html#get-project-hook */
    public function getWebhook(string $repo, string $webhookExternalId): WebhookDto
    {
        // TODO: CRITICAL! Test this.

        $repo = urlencode($repo);

        $response = $this->get("/projects/{$repo}/hooks/{$webhookExternalId}");
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.gitlab.com/ee/api/projects.html#list-project-hooks */
    public function getRepoWebhooks(string $repo): WebhookDataCollection
    {
        // TODO: CRITICAL! Test this.

        $repo = urlencode($repo);

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

    /** https://docs.gitlab.com/ee/api/projects.html#add-project-hook */
    public function addWebhookPush(string $repo, string $payloadUrl, string $secret): WebhookDto
    {
        // TODO: CRITICAL! Test this.

        $repo = urlencode($repo);

        $response = $this->post(
            "/repos/{$repo}/hooks",
            $this->pushWebhookRequestData($payloadUrl, $secret),
        );
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.gitlab.com/ee/api/projects.html#edit-project-hook */
    public function updateWebhookPush(string $repo, string $webhookExternalId, string $payloadUrl, string $secret): WebhookDto
    {
        // TODO: CRITICAL! Test this.

        $repo = urlencode($repo);

        $response = $this->put(
            "/repos/{$repo}/hooks",
            $this->pushWebhookRequestData($payloadUrl, $secret),
        );
        $data = $this->decodeJson($response->body());

        return $this->webhookDataFromResponseData($data);
    }

    /** https://docs.gitlab.com/ee/api/projects.html#delete-project-hook */
    public function deleteWebhook(string $repo, string $webhookExternalId): void
    {
        // TODO: CRITICAL! Test this.

        $repo = urlencode($repo);

        $this->delete("/repos/{$repo}/hooks/{$webhookExternalId}");
    }

    /** https://docs.gitlab.com/ee/api/oauth2.html#authorization-code-flow */
    public function refreshToken(string $refreshToken): OAuthTokenDto
    {
        $redirect = config('services.gitlab.redirect');

        $requestData = [
            'client_id' => config('services.gitlab.client_id'),
            'client_secret' => config('services.gitlab.client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'redirect_uri' => Str::startsWith($redirect, '/')
                ? URL::to($redirect)
                : $redirect,
        ];

        $response = $this->request()->post('https://gitlab.com/oauth/token', $requestData)->throw();

        $data = $this->decodeJson($response->body());

        return OAuthTokenDto::fromData(
            $data->access_token,
            $data->refresh_token,
            $data->expires_in,
        );
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

    /** Convert webhook data from response format into the internal format. */
    protected function webhookDataFromResponseData(object $data): WebhookDto
    {
        return new WebhookDto(
            events: $this->eventsArrayFromData($data),
            id: (string) $data->id,
            url: $data->config->url,
        );
    }

    /** Convert webhook data from a response to an array of events. */
    protected function eventsArrayFromData(object $data): array
    {
        // https://docs.gitlab.com/ee/api/projects.html#add-project-hook
        $events = [
            "push",
            "issues",
            "confidential_issues",
            "merge_requests",
            "tag_push",
            "note",
            "confidential_note",
            "job",
            "pipeline",
            "wiki_page",
            "deployment",
            "releases",
        ];

        $result = [];
        foreach ($events as $event) {
            $name = "{$events}_events";

            if ($data->$name)
                $result[] = $event;
        }

        return $result;
    }

    /** Get a request data array for creating or updating a push webhook. */
    protected function pushWebhookRequestData(string $payloadUrl, string $secret): array
    {
        return [
            'push_events' => true,
            'url' => $payloadUrl,
            'token' => $secret,
            'enable_ssl_verification' => true,
        ];
    }
}
