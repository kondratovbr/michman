<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class UpdateDatabaseUserPasswordJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        $this->setQueue('servers');

        $this->databaseUser = $databaseUser;

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

            $script = $this->getDatabaseScript($server, 'UpdateDatabaseUserPasswordScript');

            $script->execute(
                $server,
                $databaseUser->name,
                $databaseUser->password,
            );

            $databaseUser->password = null;
            $databaseUser->save();

            $databaseUser->decrementTasks();
        }, 5);
    }
}
