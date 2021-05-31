<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallPythonJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string $pythonVersion;

    public function __construct(Server $server, string $pythonVersion)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
        $this->pythonVersion = $pythonVersion;
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

            if (! Arr::hasValue(config(""), 'python')) {
                $this->fail(new RuntimeException('This type of server should not have Python installed.'));
                return;
            }

            if ($server->pythons()->where('version', $this->pythonVersion)->get()->isNotEmpty()) {
                $this->fail(new RuntimeException('This Python version model is already created for this server.'));
                return;
            }

            $python = $server->pythons()->create([
                'version' => $this->pythonVersion,
            ]);

            // TODO: CRITICAL! CONTINUE!

            //
        }, 5);
    }
}
