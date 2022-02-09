<?php declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Webhooks\DeleteProjectWebhookAction;
use App\Jobs\Users\DeleteUserJob;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteUserAction
{
    public function __construct(
        private DeleteProjectWebhookAction $deleteWebhook,
    ) {}

    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->freshLockForUpdate();

            $jobs = new Collection;

            $jobs = $jobs->concat($this->deleteWebhooksJobs($user));

            $jobs->push(new DeleteUserJob($user));

            Bus::chain($jobs->toArray())->dispatch();

            $user->isDeleting = true;
            $user->save();
        }, 5);
    }

    protected function deleteWebhooksJobs(User $user): Collection
    {
        return $user->webhooks->map(fn(Webhook $hook) =>
            $this->deleteWebhook->execute($hook, true)
        );
    }
}
