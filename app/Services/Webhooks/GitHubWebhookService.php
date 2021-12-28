<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Services\Traits\HasConfig;
use App\Support\Arr;
use App\Support\Str;
use Illuminate\Http\Request;

// TODO: IMPORTANT! Cover with tests.

class GitHubWebhookService implements WebhookServiceInterface
{
    use HasConfig;

    private const SIGNATURE_HEADER_NAME = 'X-Hub-Signature-256';
    private const EVENT_HEADER_NAME = 'X-GitHub-Event';
    private const EXTERNAL_ID_HEADER_NAME = 'X-GitHub-Delivery';

    public function __construct()
    {
        $this->setConfigPrefix('webhooks.providers.github');
    }

    public function signatureValid(Request $request, string $secret): bool
    {
        if (! $request->hasHeader(self::SIGNATURE_HEADER_NAME))
            return false;

        $signatureProvided = $request->header(self::SIGNATURE_HEADER_NAME);

        $signatureComputed = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($signatureProvided, $signatureComputed);
    }

    public function getEventName(Request $request): string|null
    {
        if (! $request->hasHeader(self::EVENT_HEADER_NAME))
            return null;

        return $request->header(self::EVENT_HEADER_NAME);
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
        if (! $request->hasHeader(self::EXTERNAL_ID_HEADER_NAME))
            return null;

        return $request->header(self::EXTERNAL_ID_HEADER_NAME);
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
