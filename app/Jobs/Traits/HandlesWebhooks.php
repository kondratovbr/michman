<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Jobs\AbstractJob;
use App\Jobs\Exceptions\WrongWebhookCallTypeException;
use App\States\Webhooks\Deleting;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * @mixin AbstractJob
 */
trait HandlesWebhooks
{
    /** Delete the job if its models no longer exist. */
    public bool $deleteWhenMissingModels = true;

    /** @var string Expected webhook call type. */
    protected string $callType;

    /** Execute the job. */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->call = $this->call->freshLockForUpdate('webhook');

            if ($this->call->type !== $this->callType)
                throw new WrongWebhookCallTypeException($this->callType, $this->call->type);

            if (! $this->call->webhook->state->is(Deleting::class)) {
                app()->call([$this, 'execute']);
            }

            $this->call->processed = true;
            $this->call->save();
        }, 5);
    }

    /** Perform the actions specific to the hook. */
    public function execute(): void
    {
        throw new RuntimeException('Method execute() should be overridden to run the webhook-specific actions.');
    }
}
