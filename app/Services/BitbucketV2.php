<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\SshKeyDataCollection;
use App\Collections\WebhookDataCollection;
use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// TODO: IMPORTANT! Cover with tests.

// TODO: CRITICAL! CONTINUE.

class BitbucketV2 extends AbstractVcsProvider
{
    protected function getConfigPrefix(): string
    {
        return 'vcs.list.bitbucket_v2';
    }

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token->token);
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

    public function getAllSshKeys(): SshKeyDataCollection
    {
        // TODO: Implement getAllSshKeys() method.
    }

    public function getSshKey(string $sshKeyExternalId): SshKeyDto
    {
        // TODO: Implement getSshKey() method.
    }

    public function addSshKey(string $name, string $publicKey): SshKeyDto
    {
        // TODO: Implement addSshKey() method.
    }

    public function updateSshKey(SshKeyDto $sshKey): SshKeyDto
    {
        // TODO: Implement updateSshKey() method.
    }

    public function deleteSshKey(string $id): void
    {
        // TODO: Implement deleteSshKey() method.
    }

    public function getSshHostKey(): string
    {
        // TODO: Implement getSshHostKey() method.
    }

    public function getLatestCommitHash(?string $fullRepoName, string $branch, string $username = null, string $repo = null,): string
    {
        // TODO: Implement getLatestCommitHash() method.
    }

    public static function getFullSshString(string $repo): string
    {
        // TODO: Implement getFullSshString() method.
    }

    public function getWebhook(string $repo, string $webhookExternalId): WebhookDto
    {
        // TODO: Implement getWebhook() method.
    }

    public function getRepoWebhooks(string $repo): WebhookDataCollection
    {
        // TODO: Implement getRepoWebhooks() method.
    }

    public function addWebhookPush(string $repo, string $payloadUrl, string $secret): WebhookDto
    {
        // TODO: Implement addWebhookPush() method.
    }

    public function updateWebhookPush(string $repo, string $webhookExternalId, string $payloadUrl, string $secret,): WebhookDto
    {
        // TODO: Implement updateWebhookPush() method.
    }

    public function deleteWebhook(string $repo, string $webhookExternalId): void
    {
        // TODO: Implement deleteWebhook() method.
    }

    public function dispatchesPingWebhookCalls(): bool
    {
        // TODO: Implement dispatchesPingWebhookCalls() method.
    }
}
