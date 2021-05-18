<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateWorkerSshKeyForServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** The number of times the job may be attempted. */
    public int $tries = 5;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

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
