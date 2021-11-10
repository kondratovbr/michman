<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\User;
use App\Http\Livewire\Providers\CreateProviderForm;
use App\Services\ServerProviderInterface;
use App\Support\Str;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateProviderTest extends AbstractFeatureTest
{
    public function test_provider_with_token_can_be_created()
    {
        // Just in case it so happens to be disabled,
        // so validation doesn't fail at it.
        config(['providers.list.digital_ocean_v2.disabled' => false]);

        $data = [
            'provider' => 'digital_ocean_v2',
            'token' => 'foobar',
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('digital_ocean_v2_servers', ServerProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('credentialsAreValid')->once()->andReturn(true);
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertCount(1, $user->providers);
        $this->assertDatabaseHas('providers', [
            'user_id' => $user->id,
            'provider' => 'digital_ocean_v2',
            'name' => 'Valid name',
        ]);

        /** @var Provider $provider */
        $provider = $user->providers->first();

        $this->assertEquals('foobar', $provider->token->token);
    }

    public function test_provider_with_invalid_token_cannot_be_created()
    {
        // Just in case it so happens to be disabled,
        // so validation doesn't fail at it.
        config(['providers.list.digital_ocean_v2.disabled' => false]);

        $data = [
            'provider' => 'digital_ocean_v2',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
            'name' => 'Valid name for an invalid provider',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('digital_ocean_v2_servers', ServerProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('credentialsAreValid')->once()->andReturnFalse();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasErrors(['token']);

        $user->refresh();

        $this->assertCount(0, $user->providers);
    }

    public function test_disabled_provider_cannot_be_created()
    {
        config(['providers.list.digital_ocean_v2.disabled' => true]);

        $data = [
            'provider' => 'aws',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('aws_servers', ServerProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldNotHaveBeenCalled();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasErrors(['provider']);

        $user->refresh();

        $this->assertCount(0, $user->providers);
    }
}
