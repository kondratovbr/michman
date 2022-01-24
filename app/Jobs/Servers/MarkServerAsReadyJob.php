<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use App\States\Servers\Ready;

// TODO: IMPORTANT! Cover with tests!

class MarkServerAsReadyJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;

    public function __construct(Server $server)
    {
        parent::__construct();

        $this->server = $server->withoutRelations();
    }

    public function handle(): void
    {
        $this->server->state->transitionTo(Ready::class);
    }
}
