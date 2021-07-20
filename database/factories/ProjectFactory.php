<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
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
        ];
    }

    //
}
