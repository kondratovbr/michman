<?php declare(strict_types=1);

namespace App\Jobs\WorkerSshKeys;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateWorkerSshKeyForServerJob extends AbstractJob
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
    public function handle(CreateWorkerSshKeyAction $action): void
    {
        DB::transaction(function () use ($action) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (isset($server->workerSshKey)) {
                $this->fail(new RuntimeException('The server already has a workerSshKey.'));
                return;
            }

            $action->execute($server);
        }, 5);
    }
}
