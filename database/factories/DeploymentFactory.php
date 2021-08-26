<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Deployment;
use App\Models\Project;
use App\Models\Server;
use App\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeploymentFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Deployment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'branch' => 'master',
            'commit' => Str::random(8),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Deployment $deployment) {
            $deployment->servers()->sync($deployment->project->servers);
            /** @var Server $server */
            foreach ($deployment->servers as $server) {
                $server->serverDeployment->forceFill([
                    'started_at' => now(),
                    'finished_at' => now(),
                    'successful' => true,
                ])->save();
            }
        });
    }

    public function forRandomProjectFrom(Collection $projects): static
    {
        $projects = $projects->shuffle();

        return $this->afterMaking(fn(Deployment $deployment) =>
            $deployment->project()->associate($projects->random())
        )->afterCreating(fn(Deployment $deployment) =>
            $deployment->servers()->sync($deployment->project->servers)
        );
    }

    public function withProject(): static
    {
        return $this->for(Project::factory()->withUserAndServers());
    }
}
