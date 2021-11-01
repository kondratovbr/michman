<?php

namespace Tests\Feature\VcsProviders;

use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\OAuthTokenDto;
use App\DataTransferObjects\VcsProviderDto;
use App\Events\VcsProviders\VcsProviderUpdatedEvent;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateVcsProviderActionTest extends AbstractFeatureTest
{
    public function test_provider_gets_updated()
    {
        /** @var VcsProvider $vcs */
        $vcs = VcsProvider::factory()->withUser()->create();

        /** @var UpdateVcsProviderAction $action */
        $action = $this->app->make(UpdateVcsProviderAction::class);

        $data = new VcsProviderDto(
            'github_v3', '123', 'TheOtherName',
            new OAuthTokenDto('98765'),
        );

        Event::fake();

        $action->execute($vcs, $data);

        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcs->id,
            'user_id' => $vcs->user->id,
            'provider' => 'github_v3',
            'external_id' => '123',
            'nickname' => 'TheOtherName',
        ]);

        $vcs->refresh();

        $this->assertEquals('98765', $vcs->token);

        Event::assertDispatched(VcsProviderUpdatedEvent::class);
    }
}
