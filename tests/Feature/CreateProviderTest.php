<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\User;
use App\Http\Livewire\Providers\CreateProviderForm;
use App\Support\Str;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateProviderTest extends AbstractFeatureTest
{
    public function test_provider_with_token_can_be_created()
    {
        $data = [
            'provider' => 'digital_ocean_v2',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mock('digital_ocean_v2', function (MockInterface $mock) {
            $mock->shouldReceive('setToken')->once();
            $mock->shouldReceive('credentialsAreValid')->once()->andReturnTrue();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasNoErrors();

        $user->fresh();

        $this->assertCount(1, $user->providers);
        $this->assertDatabaseHas(Provider::tableName(), $data);
    }

    public function test_provider_with_basic_auth_can_be_created()
    {
        $data = [
            'provider' => 'aws',
            'token' => null,
            'key' => Str::random(16),
            'secret' => Str::random(16),
            'name' => 'Valid name',
        ];

        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user);

        $this->mock('aws', function (MockInterface $mock) {
            $mock->shouldReceive('setToken')->once();
            $mock->shouldReceive('credentialsAreValid')->once()->andReturnTrue();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasNoErrors();

        $user->fresh();

        $this->assertCount(1, $user->providers);
        $this->assertDatabaseHas(Provider::tableName(), $data);
    }

    public function test_provider_with_wrong_type_of_credentials_cannot_be_created()
    {
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

        $this->mock('digital_ocean_v2', function (MockInterface $mock) {
            $mock->shouldNotHaveBeenCalled();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasErrors(['token']);

        $user->fresh();

        $this->assertCount(0, $user->providers);
    }

    public function test_provider_with_invalid_token_cannot_be_created()
    {
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

        $this->mock('digital_ocean_v2', function (MockInterface $mock) {
            $mock->shouldReceive('setToken')->once();
            $mock->shouldReceive('credentialsAreValid')->once()->andReturnFalse();
        });

        Livewire::test(CreateProviderForm::class)
            ->set($data)
            ->call('store')
            ->assertHasErrors(['token']);

        $user->fresh();

        $this->assertCount(0, $user->providers);
    }
}
