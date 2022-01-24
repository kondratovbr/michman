<?php declare(strict_types=1);

namespace App\Jobs\Pythons;

use App\Actions\Pythons\StorePythonAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreatePythonJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected string $version;
    protected bool $sync;

    public function __construct(Server $server, string $version, bool $sync = false)
    {
        parent::__construct();

        $this->server = $server->withoutRelations();
        $this->version = $version;
        $this->sync = $sync;
    }

    public function handle(StorePythonAction $storePython): void
    {
        DB::transaction(function () use ($storePython) {
            $server = $this->server->freshSharedLock();

            $storePython->execute($this->version, $server, $this->sync);
        }, 5);
    }
}
