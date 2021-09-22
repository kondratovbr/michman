<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTransferObjects\DeploymentDto;
use App\Exceptions\NotImplementedException;
use App\Http\Exceptions\InvalidWebhookSignatureException;
use App\Http\Exceptions\WebhookEventNotSupportedException;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends AbstractController
{
    // TODO: CRITICAL! Don't forget that services (see GitHub) want me to actually send some valid response to them with some actual info. See their docs.

    /** Handle a received webhook. */
    public function __invoke(string $webhookProvider, Webhook $webhook, Request $request): mixed
    {
        ray('WebhookController::push()');

        $this->validateWebhookProvider($webhookProvider, $webhook);

        $this->validateEvent($request, $webhook);

        $this->validateSignature($request, $webhook);

        ray('WebhookController - A OK!');

        return 'OK';

        // TODO: CRITICAL! Don't forget that I must update the webhook when user changes the deployment branch. And when they change the repo, of course.

        // TODO: CRITICAL! CONTINUE. Unfinished.

        throw new NotImplementedException;

        // Validation!
        //

        // /** @var Project $project */
        // $project = Project::query()->where('webhook_url_key', $hookUrlKey)->firstOrFail();

        $action->execute(new DeploymentDto(
            //
        ), $project);

        //
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
