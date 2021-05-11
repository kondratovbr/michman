<?php

namespace Tests\Feature;

use App\Actions\Providers\StoreProviderAction;
use App\DataTransferObjects\ProviderData;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Tests\AbstractFeatureTest;

class StoreProviderActionTest extends AbstractFeatureTest
{
    public function test_provider_with_token_gets_created()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $data = new ProviderData(
            owner: $user,
            provider: 'digital_ocean_v2',
            token: 'foobar',
            key: null,
            secret: null,
            name: 'New Provider',
        );

        /** @var StoreProviderAction $action */
        $action = App::make(StoreProviderAction::class);

        $provider = $action->execute($data);

        $this->assertNotNull($provider);
        $this->assertNotNull($provider->id);
        $this->assertCount(1, $user->providers);
        $this->assertEquals('digital_ocean_v2', $provider->provider);
        $this->assertEquals('New Provider', $provider->name);
        $this->assertEquals('foobar', $provider->token);
        $this->assertNull($provider->key);
        $this->assertNull($provider->secret);
        // We don't assert for token here because token is stored encrypted,
        // so we cannot easily check it.
        $this->assertDatabaseHas('providers', [
            'user_id' => $user->id,
            'provider' => 'digital_ocean_v2',
            'name' => 'New Provider',
            'key' => null,
            'secret' => null,
        ]);

        $provider->fresh();

        $this->assertEquals('foobar', $provider->token);
    }

    public function test_provider_with_key_and_secret_gets_created()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $data = new ProviderData(
            owner: $user,
            provider: 'aws',
            token: null,
            key: 'login',
            secret: 'password',
            name: 'New Provider',
        );

        /** @var StoreProviderAction $action */
        $action = App::make(StoreProviderAction::class);

        $provider = $action->execute($data);

        $this->assertNotNull($provider);
        $this->assertNotNull($provider->id);
        $this->assertCount(1, $user->providers);
        $this->assertEquals('aws', $provider->provider);
        $this->assertEquals('New Provider', $provider->name);
        $this->assertNull($provider->token);
        $this->assertEquals('login', $provider->key);
        $this->assertEquals('password', $provider->secret);
        // We don't assert for key/secret here because they're stored encrypted,
        // so we cannot easily check them.
        $this->assertDatabaseHas('providers', [
            'user_id' => $user->id,
            'provider' => 'aws',
            'name' => 'New Provider',
            'token' => null,
        ]);

        $provider->fresh();

        $this->assertEquals('login', $provider->key);
        $this->assertEquals('password', $provider->secret);
    }
}
