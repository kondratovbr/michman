<?php declare(strict_types=1);

namespace App\Jobs\DatabaseUsers;

use App\Jobs\AbstractRemoteServerJob;
use App\Jobs\Traits\HandlesDatabases;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\DB;

class CreateDatabaseUserOnServerJob extends AbstractRemoteServerJob
{
    use HandlesDatabases;

    protected DatabaseUser $databaseUser;

    public function __construct(DatabaseUser $databaseUser)
    {
        parent::__construct($databaseUser->server);

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

            /*
             * TODO: IMPORTANT! I can't delete DB user's password here - I use it to create a project's default environment.
             *       Maybe I can delete it somewhere later for added security?
             *       Like, after creating that default .env? Can it be used again?
             */

            $databaseUser->decrementTasks();
        }, 5);
    }
}
