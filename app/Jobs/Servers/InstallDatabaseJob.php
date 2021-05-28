<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallDatabaseJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string|null $database;

    public function __construct(Server $server, string|null $database)
    {
        $this->setQueue('servers');

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

            if (! is_null($server->installedDatabase))
                $this->fail(new RuntimeException('Server already has a database installed.'));

            if (empty($server->databaseRootPassword)) {
                $server->databaseRootPassword = Str::random(32);
                $server->save();
                // We release the job here so the transaction will commit and save the password.
                // This way we don't have to run this job in two transactions every time.
                $this->release();
            }

            $scriptClass = (string) config("servers.databases.{$this->database}.scripts_namespace") . '\InstallDatabaseScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this database.');

            $script = App::make($scriptClass);

            $script->execute($server);

            $server->installedDatabase = $this->database;

            $server->save();
        }, 5);
    }
}
