<?php declare(strict_types=1);

namespace App\Actions\ServerSshKeys;

use App\Models\Server;
use App\Models\ServerSshKey;
use phpseclib3\Crypt\EC;

class CreateServerSshKeyAction
{
    public function execute(Server $server, bool $addToVcs): ServerSshKey
    {
        $key = EC::createKey('Ed25519');

        /** @var ServerSshKey $serverKey */
        $serverKey = $server->serverSshKey()->make([
            'name' => ServerSshKey::createName($server),
            'add_to_vcs' => $addToVcs,
        ]);

        $serverKey->privateKey = $key;
        $serverKey->publicKey = $key->getPublicKey();

        $serverKey->save();

        return $serverKey;
    }
}
