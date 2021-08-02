<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Deployment;
use App\Support\Str;
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
            'completed_at' => $this->faker->dateTimeBetween(now()->subDays(7), now()),
        ];
    }
}
