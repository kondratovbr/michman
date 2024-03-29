<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        return [
            'region' => 'nyc1',
            'size' => 's-1vcpu-1gb',
            'name' => generateRandomName(),
            // TODO: Seed other types as well.
            'type' => 'app',
            'ssh_port' => (string) config('servers.default_ssh_port'),
            // TODO: Seed others as well.
            'installed_database' => 'mysql-8_0',
            'state' => 'ready',
        ];
    }

    /**
     * Also create a provider owning this server.
     *
     * @return $this
     */
    public function withProvider(): static
    {
        return $this->state([
            'provider_id' => Provider::factory()->withOwner(),
        ]);
    }

    /**
     * Attach this server to a random provider from a collection provided.
     *
     * @return $this
     */
    public function forRandomProviderFrom(Collection $providers): static
    {
        return $this->afterMaking(fn(Server $server) =>
            $this->associateProvider($server, $providers->random())
        );
    }

    /** Attach server to a provider. */
    protected function associateProvider(Server $server, Provider $provider): void
    {
        $server->provider()->associate($provider);
    }
}
