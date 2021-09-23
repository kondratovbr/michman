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
}
