<?php declare(strict_types=1);

namespace App\Jobs\Servers;

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

    public function __construct(Server $server, string $dbName)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->dbName = $dbName;
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
            ), $server);
        }, 5);
    }
}
