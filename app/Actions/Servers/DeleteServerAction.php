<?php declare(strict_types=1);

namespace App\Actions\Servers;

// TODO: Cover with tests.

use App\Models\Server;

class DeleteServerAction
{
    public function execute(Server $server): void
    {
        // TODO: CRITICAL! CONTINUE. Implement.

        ray('Deleting server ' . $server->name);

        //
    }
}
