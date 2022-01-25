<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Scripts\Root\DeleteUserScript;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteUserFromServerJob extends AbstractRemoteServerJob
{
    protected string $username;

    public function __construct(Server $server, string $username)
    {
        parent::__construct($server);

        $this->username = $username;
    }

    public function handle(DeleteUserScript $deleteUser): void
    {
        DB::transaction(function () use ($deleteUser) {
            $server = $this->server->freshLockForUpdate();

            $deleteUser->execute($server, $this->username);
        }, 5);
    }
}
