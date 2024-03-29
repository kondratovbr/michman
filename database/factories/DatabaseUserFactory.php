<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatabaseUserFactory extends Factory
{
    protected $model = DatabaseUser::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->userName,
            'password' => null,
        ];
    }

    /**
     * Also create a server for this database user.
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
     * Attach database users to random servers from a collection.
     *
     * @return $this
     */
    public function forRandomServerFrom(Collection $servers): static
    {
        return $this->afterMaking(
            fn(DatabaseUser $databaseUser) => $this->associateServer($databaseUser, $servers->random())
        );
    }

    /**
     * Attach database users to random databases on their respective servers, if any exist.
     *
     * @return $this
     */
    public function attachToRandomDatabase(): static
    {
        return $this->afterCreating(function (DatabaseUser $databaseUser) {
            $databases = $databaseUser->server->databases;

            if ($databases->isNotEmpty())
                $databaseUser->databases()->attach($databases->random());
        });
    }

    /** Attach a database user to a server. */
    protected function associateServer(DatabaseUser $databaseUser, Server $server): void
    {
        $databaseUser->server()->associate($server);
    }
}
