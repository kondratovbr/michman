<?php

namespace Tests\Feature\UserSshKeys;

use App\Actions\UserSshKeys\DeleteUserSshKeyAction;
use App\Models\Provider;
use App\Models\Server;
use App\Models\UserSshKey;
use Illuminate\Database\Eloquent\Collection;
use Tests\AbstractFeatureTest;

class DeleteUserSshKeyActionTest extends AbstractFeatureTest
{
    public function test_key_gets_deletes()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->owner;
        /** @var Collection $servers */
        $servers = Server::factory()->for($provider)->count(2)->create();
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->hasAttached($servers)->create();

        /** @var DeleteUserSshKeyAction $action */
        $action = $this->app->make(DeleteUserSshKeyAction::class);

        $action->execute($userSshKey);

        $this->assertDatabaseMissing('user_ssh_keys', [
            'id' => $userSshKey->getKey(),
        ]);

        /** @var Server $server */
        foreach ($servers as $server) {
            $server->refresh();
            $this->assertEmpty($server->userSshKeys);
        }
    }
}
