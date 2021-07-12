<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateDatabaseUserOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        $this->setQueue('servers');

        $this->databaseUser = $databaseUser->withoutRelations();

        $this->databaseUser->incrementTasks();
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

            $this->getDatabaseScript(
                $server,
                'CreateDatabaseUserScript',
            )->execute(
                $server,
                $databaseUser->name,
                $databaseUser->password,
            );

            // We don't need to store the password anymore,
            // so just delete it for a bit of added security.
            $databaseUser->password = null;
            $databaseUser->save();

            $databaseUser->decrementTasks();
        }, 5);
    }
}
