<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Models\Daemon;
use App\Scripts\Root\RetrieveDaemonLogScript;
use Throwable;

class RetrieveDaemonLogAction
{
    public function __construct(
        protected RetrieveDaemonLogScript $script,
    ) {}

    public function execute(Daemon $daemon): string|false
    {
        try {
            return retry(
                5,
                fn() => $this->script->execute($daemon->server, $daemon),
                100,
            );
        } catch (Throwable) {
            return false;
        }
    }
}
