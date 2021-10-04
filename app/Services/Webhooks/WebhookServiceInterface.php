<?php declare(strict_types=1);

namespace App\Services\Webhooks;

use Illuminate\Http\Request;

interface WebhookServiceInterface
{
    /** Check if the webhook signature is present and valid on a request. */
    public function signatureValid(Request $request, string $secret): bool;

    /** Get the webhook event name from the request. */
    public function getEventName(Request $request): string|null;

    /** Check if the webhook event received is configured to be allowed by the application. */
    public function eventIsSupported(Request $request): bool;

    /** Get the external ID (delivery ID) of a webhook delivery from a request. */
    public function getExternalId(Request $request): string|null;

    /**
     * Get the name of the branch that the commits in the push event were pushed on.
     *
     * Returns null if the commits were not on a branch.
     */
    public function pushedBranch(array $data): string|null;

    /**
     * Get the hash of the commit pushed by the push event.
     *
     * Returns null if no such info found in the call payload.
     */
    public function pushedCommitHash(array $data): string|null;
}
