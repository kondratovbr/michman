<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Jobs\AbstractJob;

/**
 * @mixin AbstractJob
 */
trait HandlesWebhooks
{
    /** Delete the job if its models no longer exist. */
    public bool $deleteWhenMissingModels = true;

    //
}
