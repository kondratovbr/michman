<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ConfigureServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** The number of times the job may be attempted. */
    public int $tries = 5;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('default');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $jobClass = (string) config('servers.types.' . $server->type . '.configuration_job_class');

            if (empty($jobClass))
                throw new \RuntimeException('No job class for this server type configured.');

            $jobClass::dispatch($server);
        }, 5);
    }
}
