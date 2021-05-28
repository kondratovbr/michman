<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\Jobs\AbstractJob;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreateWorkerSshKeyForServerJob extends AbstractJob
{
    /** The number of times the job may be attempted. */
    public int $tries = 5;
    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 5;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->queue('default');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(CreateWorkerSshKeyAction $action): void
    {
        DB::transaction(function () use ($action) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (isset($server->workerSshKey))
                $this->fail(new \RuntimeException('The server already has a workerSshKey.'));

            $action->execute($server);
        }, 5);
    }
}
