<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Python;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PythonFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Python::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // TODO: Seed other versions as well. Also seed multiple versions of Python per server.
            'version' => '3_8',
            'status' => Python::STATUS_INSTALLED,
            'patch_version' => '3.8.0',
        ];
    }

    /**
     * Set the status of this python to "installing".
     */
    public function installing(): static
    {
        return $this->state([
            'status' => Python::STATUS_INSTALLING,
        ]);
    }

    /**
     * Set the status of this python to "updating".
     */
    public function updating(): static
    {
        return $this->state([
            'status' => Python::STATUS_UPDATING,
        ]);
    }

    /**
     * Also create a server for this python.
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
     * Create Python models for a random server from collection,
     * making sure to do it no more than once for every one of them.
     *
     * @return $this
     */
    public function forRandomServerFromCollectionOnce(Collection $servers): static
    {
        $servers = $servers->shuffle();

        return $this->afterMaking(function (Python $python) use ($servers) {
            $this->associateWithServer($python, $servers->pop());
        });
    }

    /**
     * Associate a Python model with a Server.
     */
    protected function associateWithServer(Python $python, Server $server): void
    {
        $python->server()->associate($server);
    }
}
