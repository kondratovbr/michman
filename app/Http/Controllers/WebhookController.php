<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Exceptions\InvalidWebhookSignatureException;
use App\Http\Exceptions\WebhookEventNotSupportedException;
use App\Models\Webhook;
use App\Models\WebhookCall;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WebhookController extends AbstractController
{
    // TODO: CRITICAL! CONTINUE. Unfinished.

    /** Handle a received webhook. */
    public function __invoke(string $webhookProvider, Webhook $webhook, Request $request): Response
    {
        $this->validateWebhook($webhookProvider, $request, $webhook);

        DB::transaction(function () use ($request, $webhook) {
            /** @var WebhookCall $call */
            $call = $webhook->calls()->create([
                'type' => $webhook->service()->getEventName($request),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'payload' => $request->input(),
            ]);

            config("webhooks.jobs.{$call->type}")::dispatch($call);
        }, 5);

        return response(status: Response::HTTP_OK);

        // TODO: CRITICAL! Don't forget that I must update the webhook when user changes the deployment branch. And when they change the repo, of course.
    }

    protected function validateWebhook(string $webhookProvider, Request $request, Webhook $webhook): void
    {
        $this->validateWebhookProvider($webhookProvider, $webhook);

        $this->validateEvent($request, $webhook);

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

    protected function validateSignature(Request $request, Webhook $webhook): void
    {
        if (! $webhook->service()->signatureValid($request, $webhook->secret))
            throw new InvalidWebhookSignatureException;
    }
}
