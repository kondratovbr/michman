<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Database;
use App\Models\Provider;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatabaseFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Database::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName,
        ];
    }

    /**
     * Also create a server for this database.
     *
     * @return $this
     */
    public function withServer(): static
    {
        return $this->state([
            'server_id' => Server::factory()->withProvider(),
        ]);
    }

    /**
     * Attach databases to random servers from a collection.
     */
    public function forRandomServerFrom(Collection $servers): static
    {
        return $this->afterMaking(
            fn(Database $database) => $this->associateServer($database, $servers->random())
        );
    }

    /**
     * Attach a database to a server.
     */
    protected function associateServer(Database $database, Server $server): void
    {
        $database->server()->associate($server);
    }
}
