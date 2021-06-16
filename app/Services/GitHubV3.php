<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GitHubV3 extends AbstractVcsProvider
{
    /*
     * TODO: CRITICAL! CONTINUE!
     *       Refactor server provider services and VCS services to use a common base class with common function.
     *       Refactor into not using "getJson" type of methods but provide a single "Accept" header like here.
     *       Then implement a front-end part of VCS integration, see how Forge does it - pretty simple.
     *       Don't forget to create a VCS provider model when a user is logging in via VCS OAuth.
     */

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
        // TODO: Implement credentialsAreValid() method.
    }
}
