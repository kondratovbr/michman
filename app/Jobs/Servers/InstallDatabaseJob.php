<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Support\Arr;
use App\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallDatabaseJob extends AbstractRemoteServerJob
{
    protected Server $server;
    protected string|null $database;

    public function __construct(Server $server, string|null $database)
    {
        parent::__construct($server);

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

            if (! Arr::hasValue(config("servers.types.{$server->type}.install"), 'database')) {
                $this->fail(new RuntimeException('This type of server should not have a database installed.'));
                return;
            }

            if (! is_null($server->installedDatabase)) {
                $this->fail(new RuntimeException('Server already has a database installed.'));
                return;
            }

            if (empty($server->databaseRootPassword)) {
                $server->databaseRootPassword = Str::random(32);
                $server->save();
                // We release the job here so the transaction will commit and save the password.
                // This way we don't have to run this job in two transactions every time.
                $this->release();
                return;
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
