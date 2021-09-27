<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Jobs\AbstractJob;
use App\Jobs\Exceptions\WrongWebhookCallTypeException;
use App\Models\WebhookCall;

/**
 * @mixin AbstractJob
 */
trait HandlesWebhooks
{
    /** Delete the job if its models no longer exist. */
    public bool $deleteWhenMissingModels = true;

    /** Verify that we're working with a webhook that has the type we expect. */
    protected function verifyHookCallType(WebhookCall $call, string $exceptedType): void
    {
        if ($call->type !== $exceptedType)
            throw new WrongWebhookCallTypeException($exceptedType, $call->type);
    }
}
