<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Deployments\StoreDeploymentAction;
use App\DataTransferObjects\DeploymentDto;
use App\Exceptions\NotImplementedException;
use App\Models\Webhook;
use Illuminate\Http\Response;

class HookController extends AbstractController
{
    // TODO: CRITICAL! Don't forget that services (see GitHub) want me to actually send some valid response to them with some actual info. See their docs.

    /**
     * Handle "push" webhook.
     *
     * I.e. a commit has been pushed to one of the repos we're connected to.
     */
    public function push(string $webhookProvider, Webhook $webhook, StoreDeploymentAction $action): mixed
    {
        // TODO: CRITICAL! Make sure to check the "X-GitHub-Event" header.

        ray('HookController::push()');

        return 'OK!';

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
}
