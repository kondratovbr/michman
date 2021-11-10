<?php

namespace Tests\Feature\VcsProviders;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\VcsProviderDto;
use App\Events\VcsProviders\VcsProviderCreatedEvent;
use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreVcsProviderActionTest extends AbstractFeatureTest
{
    public function test_provider_gets_stored()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var StoreVcsProviderAction $action */
        $action = $this->app->make(StoreVcsProviderAction::class);

        $data = new VcsProviderDto(
            'github_v3', '123', 'TheGuy',
            new AuthTokenDto('123', '1234567890'),
        );

        Event::fake();

        $vcs = $action->execute($data, $user);

        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcs->id,
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123',
            'nickname' => 'TheGuy',
        ]);

        $this->assertCount(1, $user->vcsProviders);

        /** @var VcsProvider $vcs */
        $vcs = $user->vcsProviders()->firstOrFail();

        $this->assertEquals('1234567890', $vcs->token->token);

        Event::assertDispatched(VcsProviderCreatedEvent::class);
    }
}
