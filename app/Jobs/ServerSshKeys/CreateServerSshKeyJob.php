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
    protected bool $addToVcs;

    public function __construct(Server $server, bool $addToVcs)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->addToVcs = $addToVcs;
    }

    /**
     * Execute the job.
     */
    public function handle(CreateServerSshKeyAction $action): void
    {
        DB::transaction(function () use ($action) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $action->execute($server, $this->addToVcs);
        }, 5);
    }
}
