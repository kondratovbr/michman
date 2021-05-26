<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class InstallDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InteractsWithRemoteServers;

    protected Server $server;
    protected string $database;

    public function __construct(Server $server, string $database)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
        $this->database = $database;
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

            $scriptClass = (string) config('servers.databases.' . $this->database . '.install_script');

            if (empty($scriptClass))
                throw new \RuntimeException('No installation script configured for this database.');

            $script = App::make($scriptClass);

            $script->execute($server);

            $server->installedDatabase = $this->database;
            $server->save();
        }, 5);
    }
}
