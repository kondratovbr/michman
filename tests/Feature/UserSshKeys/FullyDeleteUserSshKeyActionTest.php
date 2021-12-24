<?php

namespace Tests\Feature\UserSshKeys;

use App\Actions\UserSshKeys\FullyDeleteUserSshKeyAction;
use App\Jobs\UserSshKeys\DeleteUserSshKeyFromServerJob;
use App\Jobs\UserSshKeys\DeleteUserSshKeyJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\UserSshKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Tests\AbstractFeatureTest;

class FullyDeleteUserSshKeyActionTest extends AbstractFeatureTest
{
    public function test_jobs_get_chained()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;
        /** @var Collection $servers */
        $servers = Server::factory()->for($provider)->count(2)->create();
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->hasAttached($servers)->create();

        /** @var FullyDeleteUserSshKeyAction $action */
        $action = $this->app->make(FullyDeleteUserSshKeyAction::class);

        Bus::fake();

        $action->execute($userSshKey);

        Bus::assertChained([
            DeleteUserSshKeyFromServerJob::class,
            DeleteUserSshKeyFromServerJob::class,
            DeleteUserSshKeyJob::class,
        ]);
    }
}
