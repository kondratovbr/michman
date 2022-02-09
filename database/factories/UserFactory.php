<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use App\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

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
     * Create an active subscription plan for the user.
     *
     * @return $this
     */
    public function withSubscription(int $planId = null): static
    {
        return $this->afterCreating(function (User $user) use ($planId) {
            $user->customer?->update(['trial_ends_at' => null]);

            $user->subscriptions()->create([
                'name' => 'default',
                'paddle_id' => random_int(1, 1000),
                'paddle_status' => 'active',
                'paddle_plan' => $planId,
                'quantity' => 1,
                'trial_ends_at' => null,
                'paused_from' => null,
                'ends_at' => null,
            ]);
        });
    }

    /**
     * Make the trial for this user to be expired.
     *
     * @return $this
     */
    public function trialExpired(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->customer->update([
                'trial_ends_at' => now()->subMinutes(5),
            ]);
        });
    }

    /**
     * Have a TFA enabled for the user.
     *
     * @return $this
     */
    public function tfaEnabled(): static
    {
        return $this->state([
            'two_factor_secret' => Str::random(),
        ]);
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

    /**
     * Create a user that is registered via GitHub OAuth.
     *
     * @return $this
     */
    public function viaGithub(array $oauthUserAttributes = []): static
    {
        return $this
            ->state(['password' => null])
            ->afterCreating(function (User $user) use ($oauthUserAttributes) {
                $user->oauthUsers()->create(Arr::merge([
                    'provider' => 'github',
                    'oauth_id' => random_int(1, 1000),
                    'nickname' => $this->faker->userName,
                ], $oauthUserAttributes));
            });
    }
}
