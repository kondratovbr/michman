<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\VcsProvider;
use App\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class VcsProviderFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = VcsProvider::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // TODO: Seed other providers as well after implementing them.
            'provider' => 'github',
            'external_id' => Str::random(8),
            'nickname' => $this->faker->userName,
            'token' => Str::random(),
        ];
    }

    /**
     * Attach this VCS provider to a random user from a collection provided.
     *
     * @return self
     */
    public function forRandomUserOnceFrom(Collection $users): self
    {
        $users = $users->shuffle();

        return $this->afterMaking(function (VcsProvider $vcsProvider) use ($users) {
            if ($users->isEmpty())
                return;

            $this->associateUser($vcsProvider, $users->pop());
        });
    }

    /**
     * Attach VCS provider to a user.
     */
    private function associateUser(VcsProvider $vcsProvider, User $user): void
    {
        $vcsProvider->user()->associate($user);
    }
}
