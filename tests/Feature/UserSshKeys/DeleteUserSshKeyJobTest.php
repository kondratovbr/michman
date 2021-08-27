<?php

namespace Tests\Feature\UserSshKeys;

use App\Actions\UserSshKeys\DeleteUserSshKeyAction;
use App\Jobs\UserSshKeys\DeleteUserSshKeyJob;
use App\Models\UserSshKey;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteUserSshKeyJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->withUser()->create();

        $this->mockBind(DeleteUserSshKeyAction::class, function (MockInterface $mock) use ($userSshKey) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(UserSshKey $keyArg) => $keyArg->is($userSshKey))
                ->once();
        });

        $job = new DeleteUserSshKeyJob($userSshKey);

        $this->assertNull( $job->queue);

        $this->app->call([$job, 'handle']);
    }
}
