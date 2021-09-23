<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Daemon;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class DaemonFactory extends Factory
{
    protected $model = Daemon::class;

    public function definition(): array
    {
        return [
            'command' => '/usr/bin/python3 --help',
            'username' => 'admin',
            'directory' => '/home/admin',
            'processes' => 2,
            'start_seconds' => 10,
            'state' => 'starting',
        ];
    }

    /**
     * Also create a server for this daemon.
     *
     * @return $this
     */
    public function withServer(): static
    {
        return $this->for(Server::factory()->withProvider());
    }

    /**
     * Set the created Daemon model to this state.
     *
     * @return $this
     */
    public function inState(string $state): static
    {
        return $this->state([
            'state' => $state,
        ]);
    }
}
