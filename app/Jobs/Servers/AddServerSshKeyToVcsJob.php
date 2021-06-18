<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\NotImplementedException;
use App\Jobs\AbstractJob;
use App\Models\Server;

class AddServerSshKeyToVcsJob extends AbstractJob
{
    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        //
    }
}
