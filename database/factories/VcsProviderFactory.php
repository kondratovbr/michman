<?php declare(strict_types=1);

namespace Database\Factories;

use App\DataTransferObjects\AuthTokenDto;
use App\Models\User;
use App\Models\VcsProvider;
use App\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class VcsProviderFactory extends Factory
{
    protected $model = VcsProvider::class;

    public function definition(): array
    {
        $id = Str::random(6);

        return [
            // TODO: Seed other providers as well after implementing them.
            'provider' => 'github_v3',
            'external_id' => $id,
            'nickname' => $this->faker->userName,
            'token' => new AuthTokenDto($id, Str::random()),
        ];
    }

    /**
     * Also create a user owning this VCS provider.
     *
     * @return $this
     */
    public function withUser(): static
    {
        return $this->state([
            'user_id' => User::factory()->withPersonalTeam(),
        ]);
    }

    /**
     * Attach this VCS provider to a random user from a collection provided.
     *
     * @return $this
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

    /** Attach VCS provider to a user. */
    private function associateUser(VcsProvider $vcsProvider, User $user): void
    {
        $vcsProvider->user()->associate($user);
    }
}
