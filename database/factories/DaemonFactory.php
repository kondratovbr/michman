<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Daemon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DaemonFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Daemon::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
