<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Actions\ServerSshKeys\CreateServerSshKeyAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreateServerSshKeyJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(CreateServerSshKeyAction $action): void
    {
        DB::transaction(function () use ($action) {
            $server = $this->server->freshSharedLock();

            $action->execute($server);
        }, 5);
    }
}
