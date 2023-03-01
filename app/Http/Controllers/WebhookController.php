<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Exceptions\InvalidWebhookSignatureException;
use App\Http\Exceptions\NoWebhookExternalIdProvidedException;
use App\Http\Exceptions\WebhookEventNotSupportedException;
use App\Models\Webhook;
use App\Models\WebhookCall;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/*
 * TODO: Refactor incoming webhook handling into using Laravel event system
 */
class WebhookController extends AbstractController
{
    /** Handle a received webhook. */
    public function __invoke(string $webhookProvider, Webhook $webhook, Request $request): Response
    {
        $this->validateWebhook($webhookProvider, $request, $webhook);

        DB::transaction(function () use ($request, $webhook) {
            $externalId = $webhook->service()->getExternalId($request);

            /*
             * If we already have this delivery stored it means it was a re-delivery for some reason,
             * and it is already processed, so we don't have to do anything.
             * We'll just return 200 to ensure idempotence.
             */
            if (! is_null(WebhookCall::query()->firstWhere('external_id', $externalId)))
                return;

            /** @var WebhookCall $call */
            $call = $webhook->calls()->create([
                'type' => $webhook->service()->getEventName($request),
                'url' => $request->fullUrl(),
                'external_id' => $externalId,
                'headers' => $request->headers->all(),
                'payload' => $request->input(),
            ]);

            config("webhooks.jobs.{$call->type}")::dispatch($call);
        }, 5);

        return response(status: Response::HTTP_OK);
    }

    protected function validateWebhook(string $webhookProvider, Request $request, Webhook $webhook): void
    {
        $this->validateWebhookProvider($webhookProvider, $webhook);

        $this->validateEvent($request, $webhook);

        $this->validateHasId($webhook, $request);

        $this->validateSignature($request, $webhook);
    }

    protected function validateWebhookProvider(string $provider, Webhook $webhook): void
    {
        if ($webhook->project->vcsProvider->webhookProvider !== $provider)
            abort(404);
    }

    protected function validateEvent(Request $request, Webhook $webhook): void
    {
        if (! $webhook->service()->eventIsSupported($request))
            throw new WebhookEventNotSupportedException;
    }

    protected function validateHasId(Webhook $webhook, Request $request): void
    {
        if (is_null($webhook->service()->getExternalId($request)))
            throw new NoWebhookExternalIdProvidedException;
    }

    protected function validateSignature(Request $request, Webhook $webhook): void
    {
        if (! $webhook->service()->signatureValid($request, $webhook->secret))
            throw new InvalidWebhookSignatureException;
    }
}
