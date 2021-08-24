<?php declare(strict_types=1);

namespace App\Actions\DeploySshKeys;

use App\Models\DeploySshKey;
use App\Models\Project;
use phpseclib3\Crypt\EC;

// TODO: CRITICAL! Cover with tests.

class CreateDeploySshKeyAction
{
    public function execute(Project $project): DeploySshKey
    {
        $key = EC::createKey('Ed25519');

        /** @var DeploySshKey $deployKey */
        $deployKey = $project->deploySshKey()->make();

        $deployKey->privateKey = $key;
        $deployKey->publicKey = $key->getPublicKey();

        $deployKey->save();

        return $deployKey;
    }
}
