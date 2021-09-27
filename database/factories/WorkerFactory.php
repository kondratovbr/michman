<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Server;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    public function definition(): array
    {
        return [
            'type' => 'celery',
            'app' => 'django_app',
            'processes' => 1,
            'queues' => ['one', 'two'],
            'stop_seconds' => 10,
            'max_tasks_per_child' => 100,
            'max_memory_per_child' => 256,
            'state' => 'starting',
        ];
    }

    /** @return $this */
    public function withServer(): static
    {
        return $this->for(Server::factory()->withProvider());
    }

    /** @return $this */
    public function inState(string $state): static
    {
        return $this->state([
            'state' => $state,
        ]);
    }
}
