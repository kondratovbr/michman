<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Services\Traits\HasConfig;
use App\Support\Arr;
use Illuminate\Http\Request;

// TODO: Cover with tests.

class BitbucketWebhookService implements WebhookServiceInterface
{
    use HasConfig;

    /**
     * Bitbucket webhook event names mapped to internal event names.
     * https://support.atlassian.com/bitbucket-cloud/docs/event-payloads/
     */
    public const EVENTS_MAP = [
        'repo:push' => 'push',
        //
    ];

    private const EVENT_HEADER_NAME = 'x-event-key';

    public function __construct()
    {
        $this->setConfigPrefix('webhooks.providers.bitbucket');
    }

    public function signatureValid(Request $request, string $secret): bool
    {
        // Bitbucket does jack shit to verify webhook calls.
        return true;
    }

    public function getEventName(Request $request): string|null
    {
        if (! $request->hasHeader(self::EVENT_HEADER_NAME))
            return null;

        $bitbucketEvent = $request->header(self::EVENT_HEADER_NAME);

        return Arr::hasKey(static::EVENTS_MAP, $bitbucketEvent)
            ? static::EVENTS_MAP[$bitbucketEvent]
            : null;
    }

    public function eventIsSupported(Request $request): bool
    {
        $event = $this->getEventName($request);

        if (is_null($event))
            return false;

        return Arr::hasValue($this->config('events'), $event);
    }

    public function getExternalId(Request $request): string|null
    {
        /*
         * Bitbucket doesn't provider webhook event ID, so we'll use the "after" hash from git.
         * Should work, since we only use "push" event webhooks anyway.
         */

        return $request->input('push.changes.0.new.target.hash');
    }

    public function pushedBranch(array $data): string|null
    {
        $branch = $data['push']['changes'][0]['new']['name'] ?? null;

        return empty($branch) ? null : $branch;
    }

    public function pushedCommitHash(array $data): string|null
    {
        $hash = $data['push']['changes'][0]['new']['target']['hash'] ?? null;

        return empty($hash) ? null : $hash;
    }
}
