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
        $workerKey = $server->workerSshKey()->make([
            'name' => WorkerSshKey::createName($server),
        ]);

        $workerKey->privateKey = $key;
        $workerKey->publicKey = $key->getPublicKey();

        $workerKey->save();

        return $workerKey;
    }
}
