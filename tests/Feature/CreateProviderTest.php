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
            'key' => null,
            'secret' => null,
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('digital_ocean_v2', ServerProviderInterface::class, function (MockInterface $mock) {
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
            'key' => null,
            'secret' => null,
            'name' => 'Valid name',
        ]);

        /** @var Provider $provider */
        $provider = $user->providers->first();

        $this->assertEquals('foobar', $provider->token);
    }

    public function test_provider_with_basic_auth_can_be_created()
    {
        // Just in case it so happens to be disabled,
        // so validation doesn't fail at it.
        config(['providers.list.aws.disabled' => false]);

        $data = [
            'provider' => 'aws',
            'token' => null,
            'key' => 'login',
            'secret' => 'password',
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('aws', ServerProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('credentialsAreValid')->once()->andReturn(true);
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertCount(1, $user->providers);
        $this->assertDatabaseHas(Provider::tableName(), [
            'user_id' => $user->id,
            'provider' => 'aws',
            'token' => null,
            'name' => 'valid name',
        ]);

        /** @var Provider $provider */
        $provider = $user->providers->first();

        $this->assertEquals('login', $provider->key);
        $this->assertEquals('password', $provider->secret);
    }

    public function test_provider_with_wrong_type_of_credentials_cannot_be_created()
    {
        // Just in case it so happens to be disabled,
        // so validation doesn't fail at it.
        config(['providers.list.digital_ocean_v2.disabled' => false]);

        $data = [
            'provider' => 'digital_ocean_v2',
            'token' => null,
            'key' => Str::random(16),
            'secret' => Str::random(16),
            'name' => 'Valid name for an invalid provider',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mockBind('aws', ServerProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldNotHaveBeenCalled();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasErrors(['token']);

        $user->refresh();

        $this->assertCount(0, $user->providers);
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

        $this->mockBind('digital_ocean_v2', ServerProviderInterface::class, function (MockInterface $mock) {
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

        $this->mockBind('aws', ServerProviderInterface::class, function (MockInterface $mock) {
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
