<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Cover with tests!

class CreateDatabaseUserOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        $this->setQueue('servers');

        $this->databaseUser = $databaseUser->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var DatabaseUser $databaseUser */
            $databaseUser = DatabaseUser::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->databaseUser->getKey());

            $server = $databaseUser->server;

            if (empty($server->installedDatabase))
                throw new RuntimeException('No database installed on this server.');

            $scriptClass = (string) config("servers.databases.{$server->installedDatabase}.scripts_namespace")
                . '\CreateDatabaseUserScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No database user creation script exists for this database.');

            $script = App::make($scriptClass);

            $script->execute($server, $databaseUser->name, $databaseUser->password);

            $databaseUser->status = DatabaseUser::STATUS_CREATED;
            $databaseUser->password = null;
            $databaseUser->save();

            event(new DatabaseUserCreatedEvent($databaseUser));
        }, 5);
    }
}
