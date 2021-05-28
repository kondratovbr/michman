<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class InstallDatabaseJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string|null $database;

    public function __construct(Server $server, string|null $database)
    {
        $this->queue('servers');

        $this->server = $server->withoutRelations();
        $this->database = $database;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (is_null($this->database) || $this->database === 'none')
            return;

        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $server->databaseRootPassword = Str::random(32);
            $server->save();
        }, 5);

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
