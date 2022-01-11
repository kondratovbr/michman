<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Services\Traits\HasConfig;
use App\Support\Arr;
use App\Support\Str;
use Illuminate\Http\Request;

// TODO: Cover with tests.

class GitLabWebhookService implements WebhookServiceInterface
{
    use HasConfig;

    private const TOKEN_HEADER_NAME = 'X-Gitlab-Token';
    private const EVENT_HEADER_NAME = 'X-Gitlab-Event';

    public function __construct()
    {
        $this->setConfigPrefix('webhooks.providers.gitlab');
    }

    /** https://docs.gitlab.com/ee/user/project/integrations/webhooks.html#validate-payloads-by-using-a-secret-token */
    public function signatureValid(Request $request, string $secret): bool
    {
        // GitLab doesn't sign webhook requests, it just adds a token we provided as a header.

        if (! $request->hasHeader(self::TOKEN_HEADER_NAME))
            return false;

        return $secret === $request->header(self::TOKEN_HEADER_NAME);
    }

    /** https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#push-events */
    public function getEventName(Request $request): string|null
    {
        $event = $request->input('event_name');

        return $event;
    }

    public function eventIsSupported(Request $request): bool
    {
        $event = $this->getEventName($request);

        if (is_null($event))
            return false;

        return Arr::hasValue($this->config('events'), $event);
    }

    /** https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#push-events */
    public function getExternalId(Request $request): string|null
    {
        /*
         * GitLab doesn't provider webhook event ID, so we'll use the "after" hash from git.
         * Should work, since we only use "push" event webhooks anyway.
         */

        $after = $request->input('after');

        return $after;
    }

    public function pushedBranch(array $data): string|null
    {
        $ref = $data['ref'] ?? null;

        if (empty($ref))
            return null;

        if (! Str::contains($ref, 'heads'))
            return null;

        return Arr::last(explode('/', trim($ref)));
    }

    public function pushedCommitHash(array $data): string|null
    {
        $hash = $data['after'] ?? null;

        return empty($hash) ? null : $hash;
    }
}
