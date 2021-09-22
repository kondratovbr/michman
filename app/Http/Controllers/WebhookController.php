<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTransferObjects\DeploymentDto;
use App\Exceptions\NotImplementedException;
use App\Http\Exceptions\InvalidWebhookSignatureException;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends AbstractController
{
    // TODO: CRITICAL! Don't forget that services (see GitHub) want me to actually send some valid response to them with some actual info. See their docs.

    /**
     * Handle "push" webhook.
     *
     * I.e. a commit has been pushed to one of the repos we're connected to.
     */
    public function push(string $webhookProvider, Webhook $webhook, Request $request): mixed
    {
        ray('WebhookController::push()');

        $this->validateWebhookProvider($webhookProvider, $webhook);

        $this->validateSignature($request, $webhook);

        // TODO: CRITICAL! Make sure to check the "X-GitHub-Event" header.

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

    protected function validateSignature(Request $request, Webhook $webhook): void
    {
        ray('WebhookController::validateSignature()');

        if (! $webhook->service()->signatureValid($request, $webhook->secret)) {
            ray('Invalid signature!');
            throw new InvalidWebhookSignatureException;
        } else {
            ray('Valid signature.');
        }
    }

    protected function validateWebhookProvider(string $provider, Webhook $webhook): void
    {
        ray('WebhookController::validateWebhookProvider()');

        if ($webhook->project->vcsProvider->webhookProvider !== $provider)
            abort(404);
    }
}
