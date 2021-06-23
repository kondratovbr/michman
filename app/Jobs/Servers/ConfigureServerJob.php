<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\AbstractJob;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class ConfigureServerJob extends AbstractJob
{
    /** The number of times the job may be attempted. */
    public int $tries = 5;
    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 10;

    protected Server $server;
    protected NewServerData $data;

    public function __construct(Server $server, NewServerData $data)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->data = $data;
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
                throw new \RuntimeException('Job class for this server type is not configured.');

            $jobClass::dispatch($server, $this->data);
        }, 5);
    }
}
