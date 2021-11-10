<?php

namespace Tests\Feature;

use App\Actions\Providers\StoreProviderAction;
use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\ProviderDto;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\AbstractFeatureTest;

class StoreProviderActionTest extends AbstractFeatureTest
{
    public function test_provider_with_token_gets_created()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $data = new ProviderDto(
            provider: 'digital_ocean_v2',
            token: new AuthTokenDto(
                null,
                'foobar',
            ),
            name: 'New Provider',
        );

        /** @var StoreProviderAction $action */
        $action = App::make(StoreProviderAction::class);

        $provider = $action->execute($data, $user);

        $this->assertNotNull($provider);
        $this->assertNotNull($provider->id);
        $this->assertCount(1, $user->providers);
        $this->assertEquals('digital_ocean_v2', $provider->provider);
        $this->assertEquals('New Provider', $provider->name);
        $this->assertEquals('foobar', $provider->token->token);
        $this->assertNull($provider->key);
        $this->assertNull($provider->secret);
        // We don't assert for token here because token is stored encrypted,
        // so we cannot easily check it.
        $this->assertDatabaseHas('providers', [
            'user_id' => $user->id,
            'provider' => 'digital_ocean_v2',
            'name' => 'New Provider',
        ]);

        $provider->refresh();

        $this->assertEquals('foobar', $provider->token->token);
    }
}
