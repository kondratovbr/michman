<?php declare(strict_types=1);

namespace App\Jobs\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\DataTransferObjects\DatabaseData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreateDatabaseJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected string $dbName;
    protected bool $sync;

    public function __construct(Server $server, string $dbName, bool $sync = false)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->dbName = $dbName;
        $this->sync = $sync;
    }

    /**
     * Execute the job.
     */
    public function handle(StoreDatabaseAction $storeDatabase): void
    {
        DB::transaction(function () use ($storeDatabase) {
            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($this->server->getKey());

            $storeDatabase->execute(new DatabaseData(
                name: $this->dbName,
            ), $server, null, $this->sync);
        }, 5);
    }
}
