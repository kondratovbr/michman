<?php

namespace Tests\Feature\UserSshKeys;

use App\Http\Livewire\UserSshKeys\UserSshKeysIndexTable;
use App\Jobs\UserSshKeys\DeleteUserSshKeyFromServerJob;
use App\Jobs\UserSshKeys\DeleteUserSshKeyJob;
use App\Jobs\UserSshKeys\UploadUserSshKeyToServerJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\UserSshKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class UserSshKeysIndexTableTest extends AbstractFeatureTest
{
    public function test_key_can_be_added_to_all_servers()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->create();
        /** @var Server $attachedServer */
        $attachedServer = Server::factory()->for($provider)->hasAttached($userSshKey)->create();
        $otherServers = Server::factory()->for($provider)->count(2)->create();

        Bus::fake();

        Livewire::actingAs($user)->test(UserSshKeysIndexTable::class)
            ->call('addToAllServers', $userSshKey->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();

        $userSshKey->refresh();

        $this->assertCount(3, $userSshKey->servers);

        /** @var Server $server */
        foreach ($otherServers as $server) {
            $this->assertCount(1, $server->userSshKeys);
            $this->assertTrue($server->userSshKeys->first()->is($userSshKey));
        }

        Bus::assertChained([
            UploadUserSshKeyToServerJob::class,
            UploadUserSshKeyToServerJob::class,
        ]);
    }

    public function test_key_can_be_removed()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;
        /** @var Collection $servers */
        $servers = Server::factory()->for($provider)->count(2)->create();
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->hasAttached($servers)->create();

        Livewire::actingAs($user)->test(UserSshKeysIndexTable::class)
            ->call('removeFromMichman', $userSshKey->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('user_ssh_keys', [
            'id' => $userSshKey->getKey(),
        ]);

        $servers->each->refresh();

        /** @var Server $server */
        foreach ($servers as $server) {
            $this->assertEmpty($server->userSshKeys);
        }
    }

    public function test_key_can_be_removed_from_servers()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;
        /** @var Collection $servers */
        $servers = Server::factory()->for($provider)->count(2)->create();
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->hasAttached($servers)->create();

        Bus::fake();

        Livewire::actingAs($user)->test(UserSshKeysIndexTable::class)
            ->call('removeFromServers', $userSshKey->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();


        Bus::assertChained([
            DeleteUserSshKeyFromServerJob::class,
            DeleteUserSshKeyFromServerJob::class,
            DeleteUserSshKeyJob::class,
        ]);
    }
}
