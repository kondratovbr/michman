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

// TODO: CRITICAL! Cover with tests!

class CreateDatabaseUserOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers,
        HandlesDatabases;

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

            $this->getDatabaseScript(
                $server,
                'CreateDatabaseUserScript',
            )->execute(
                $server,
                $databaseUser->name,
                $databaseUser->password,
            );

            if ($databaseUser->status === DatabaseUser::STATUS_CREATING)
                $databaseUser->status = DatabaseUser::STATUS_CREATED;

            // We don't need to store the password anymore,
            // so just delete it for a bit of added security.
            $databaseUser->password = null;
            $databaseUser->save();

            event(new DatabaseUserCreatedEvent($databaseUser));
        }, 5);
    }
}
