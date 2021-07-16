<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreatePythonJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected string $version;

    public function __construct(Server $server, string $version)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->version = $version;
    }

    /**
     * Execute the job.
     */
    public function handle(StorePythonAction $storePython): void
    {
        DB::transaction(function () use ($storePython) {
            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($this->server->id);

            $storePython->execute(new PythonData(
                version: $this->version,
            ), $server);
        }, 5);
    }
}
