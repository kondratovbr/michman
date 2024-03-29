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
    protected string|null $database;

    public function __construct(Server $server, string|null $database)
    {
        parent::__construct($server);

        $this->database = $database;
    }

    public function handle(): void
    {
        if (empty($this->database) || $this->database === 'none')
            return;

        DB::transaction(function () {
            $server = $this->server->freshLockForUpdate();

            if (! Arr::hasValue(config("servers.types.$server->type.install"), 'database')) {
                $this->fail(new RuntimeException('This type of server should not have a database installed.'));
                return;
            }

            if (! is_null($server->installedDatabase)) {
                $this->fail(new RuntimeException('Server already has a database installed.'));
                return;
            }

            // In case the password wasn't set during server creation.
            if (empty($server->databaseRootPassword)) {
                $server->databaseRootPassword = Str::random(32);
                $server->save();
                // We release the job here so the transaction will commit and save the password.
                // The job will repeat shorty and actually install the database.
                $this->release();
                return;
            }

            $scriptClass = config("servers.databases.$this->database.scripts_namespace") . '\InstallDatabaseScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this database.');

            $script = App::make($scriptClass);

            $script->execute($server);

            $server->installedDatabase = $this->database;
            $server->save();
        });
    }
}
