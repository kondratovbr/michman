<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return $this
     */
    public function withPersonalTeam(): static
    {
        return $this->has(
            Team::factory()
                ->state(function (array $attributes, User $user) {
                    return [
                        'name' => $user->getNameForPersonalTeam(),
                        'user_id' => $user->id,
                        'personal_team' => true,
                    ];
                }),
            'ownedTeams'
        );
    }

    /**
     * Create a predefined generic admin user.
     *
     * @return $this
     */
    public function theAdmin(): static
    {
        return $this->state([
            'email' => 'admin@example.com',
        ]);
    }

    /**
     * Crete a predefined generic user.
     *
     * @return $this
     */
    public function theUser(): static
    {
        return $this->state([
            'email' => 'user@example.com',
        ]);
    }
}
