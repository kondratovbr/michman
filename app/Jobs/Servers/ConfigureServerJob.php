<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class ConfigureServerJob extends AbstractJob
{
    /** The number of times the job may be attempted. */
    public int $tries = 5;
    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 10;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('default');

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
