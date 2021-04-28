<?php declare(strict_types=1);

namespace App\Actions\WorkerSshKeys;

use App\Models\Server;
use App\Models\WorkerSshKey;
use phpseclib3\Crypt\EC;

class CreateWorkerSshKeyAction
{
    public function execute(Server $server): WorkerSshKey
    {
        $key = EC::createKey('Ed25519');

        /** @var WorkerSshKey $workerKey */
        $workerKey = $server->workerSshKey()->create([
            'name' => $server->name,
            'private_key' => $key->toString('OpenSSH', ['comment' => $server->name]),
            'public_key' => $key->getPublicKey()->toString('OpenSSH', ['comment' => $server->name]),
        ]);

        return $workerKey;
    }
}
