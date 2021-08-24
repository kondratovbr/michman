<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\Provider;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'domain' => $this->faker->unique()->domainName,
            'aliases' => [$this->faker->unique()->domainName, $this->faker->unique()->domainName],
            'allow_sub_domains' => true,
            // TODO: Seed other types as well.
            'type' => 'django',
            'root' => 'static',
            'python_version' => '3_9',
        ];
    }

    public function forRandomUserFromCollection(Collection $users): static
    {
        $users = $users->shuffle();

        return $this->afterMaking(fn(Project $project) =>
            $this->associateUserAndServer($project, $users->pop())
        );
    }

    public function withUserAndServers(): static
    {
        return $this->state([
            'user_id' => User::factory()->withPersonalTeam(),
        ])->afterCreating(function (Project $project) {
            $server = Server::factory()->for(
                Provider::factory()->for(
                    $project->user, 'owner'
                )
            )->create();
            $project->servers()->attach($server);
        });
    }

    public function useDeployKey(): static
    {
        return $this->state([
            'use_deploy_key' => true,
        ]);
    }

    public function repoInstalled(): static
    {
        return $this->state([
            'repo' => 'user/repo',
            'branch' => 'master',
            'package' => 'the_app',
            'use_deploy_key' => false,
            // TODO: Also need to generate initial config files here.
        ]);
    }

    /**
     * Attach project to a user and to a random server of this user.
     */
    public function associateUserAndServer(Project $project, User $user)
    {
        $project->user()->associate($user);
        $project->servers()->attach($user->servers()->inRandomOrder()->first());
    }
}
