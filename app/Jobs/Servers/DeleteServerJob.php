<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Database;
use App\Models\Server;
use App\States\Servers\Deleting;
use Illuminate\Support\Facades\DB;

/*
 * TODO: Numerous failures may happen here. Need to handle somehow.
 *       Deletion from provider may fail, for example.
 */

// TODO: Cover with tests.

class DeleteServerJob extends AbstractJob
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
        DB::transaction(function () {
            $server = $this->server->freshLockForUpdate([
                'databases',
                'databaseUsers',
                'workerSshKey',
                'pythons',
                'serverSshKey',
                'firewallRules',
                'certificates',
                'workers',
                'daemons',
            ]);

            if (! $server->state->is(Deleting::class))
                return;

            if (
                $server->projects()->count() > 0
                || $server->deployments()->count() > 0
                || $server->workers()->count() > 0
            )
                return;

            $server->userSshKeys()->sync([]);

            $server->databases->each(fn(Database $database) => $database->databaseUsers()->sync([]));
            $server->databases()->delete();
            $server->databaseUsers()->delete();
            
            $server->workerSshKey()->delete();
            $server->pythons()->delete();
            $server->serverSshKey()->delete();
            $server->firewallRules()->delete();
            $server->certificates()->delete();
            $server->daemons()->delete();

            $server->provider->api()->deleteServer($server->externalId);

            $server->delete();
        }, 5);
    }
}
