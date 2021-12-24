<?php

namespace Tests\Feature;

use App\Actions\Servers\StoreServerAction;
use App\DataTransferObjects\NewServerDto;
use App\Http\Livewire\Servers\DigitalOceanForm;
use App\Models\Provider;
use App\Models\Server;
use App\Policies\ServerPolicy;
use App\Support\Str;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;
use Mockery;
use Mockery\MockInterface;

class CreateDigitalOceanServerTest extends AbstractFeatureTest
{
    public function test_server_can_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $serverName = Str::random();

        $state = [
            'provider_id' => $provider->id,
            'name' => $serverName,
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'cache' => 'redis',
        ];

        $serverData = new NewServerDto(
            name: $serverName,
            region: 'nyc_1',
            size: 'size_1',
            type: 'app',
            python_version: '3_9',
            database: 'mysql-8_0',
            cache: 'redis',
        );

        $this->actingAs($user);

        $this->mock(ServerPolicy::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('create')
                ->with($user)
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class,
                function (MockInterface $mock) use ($serverData, $provider) {
                    $mock->expects('execute')
                        ->withArgs(function (
                            NewServerDto $dataArg,
                            Provider $providerArg,
                        ) use ($serverData, $provider) {
                            return $providerArg->is($provider)
                                && $dataArg->name === $serverData->name
                                && $dataArg->region === $serverData->region
                                && $dataArg->size === $serverData->size
                                && $dataArg->type === $serverData->type
                                && $dataArg->python_version === $serverData->python_version
                                && $dataArg->database === $serverData->database
                                && $dataArg->cache === $serverData->cache;
                        })
                        ->once()
                        ->andReturn(new Server);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors();
    }

    public function test_server_with_invalid_provider_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => 100500,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.provider_id']);
    }

    public function test_server_with_invalid_region_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'inv_reg',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.region']);
    }

    public function test_server_with_invalid_size_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'inv_size',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.size']);
    }

    public function test_server_with_invalid_type_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->set('state.type', 'foobar')
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.type']);
    }

    public function test_server_with_invalid_python_version_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '666',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.python_version']);
    }

    public function test_server_with_invalid_database_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'yandere_simulator_1.2',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.database']);
    }

    public function test_server_with_invalid_cache_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => Str::random(),
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'i_am_rich',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.cache']);
    }

    public function test_server_without_name_cannot_be_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->user;

        $state = [
            'provider_id' => $provider->id,
            'name' => null,
            'region' => 'nyc_1',
            'size' => 'size_1',
            'type' => 'app',
            'python_version' => '3_9',
            'database' => 'mysql-8_0',
            'db_name' => 'app_db',
            'cache' => 'redis',
            'add_ssh_key_to_vcs' => true,
        ];

        $this->actingAs($user);

        Livewire::test(DigitalOceanForm::class)
            ->set('providers', [$provider->id => 'Provider 1', 666 => 'Provider 666'])
            ->set('availableRegions', ['nyc_1' => 'New York 1', 'nyc_2' => 'New York 2'])
            ->set('availableSizes', ['size_1' => 'Size 1', 'size_2' => 'Size 2'])
            ->set('state', $state)
            ->call('store', Mockery::mock(StoreServerAction::class))
            ->assertHasErrors(['state.name']);
    }
}
