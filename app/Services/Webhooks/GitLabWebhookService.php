<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Services\Traits\HasConfig;
use App\Support\Arr;
use Illuminate\Http\Request;

class GitLabWebhookService implements WebhookServiceInterface
{
    use HasConfig;

    protected string $configPrefix = 'webhooks.providers.gitlab';

    /*
     * TODO: CRITICAL! CONTINUE. Figure out how GitLab sings the webhooks (if at all) and implement here.
     *       Then - test webhook delivery and subsequent deployment.
     */

    // private const SIGNATURE_HEADER_NAME = 'X-Hub-Signature-256';
    private const EVENT_HEADER_NAME = 'x-gitlab-event';
    // private const EXTERNAL_ID_HEADER_NAME = 'X-GitHub-Delivery';

    public function signatureValid(Request $request, string $secret): bool
    {
        // TODO: Implement signatureValid() method.
    }

    public function getEventName(Request $request): string|null
    {
        // TODO: Implement getEventName() method.
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
        // TODO: Implement getExternalId() method.
    }

    public function pushedBranch(array $data): string|null
    {
        // TODO: Implement pushedBranch() method.
    }

    public function pushedCommitHash(array $data): string|null
    {
        // TODO: Implement pushedCommitHash() method.
    }
}
