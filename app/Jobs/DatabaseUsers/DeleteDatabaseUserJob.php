<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseUserJob extends AbstractJob
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
                'DeleteDatabaseUserScript',
            )->execute(
                $server,
                $databaseUser->name,
            );

            $databaseUser->delete();
        }, 5);
    }
}
