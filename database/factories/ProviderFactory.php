<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Provider;
use App\Models\User;
use App\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            // TODO: Seed some others as well, but keep this as default.
            'provider' => 'digital_ocean_v2',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
            'name' => $this->faker->domainName,
        ];
    }

    /**
     * Also create a user owning this provider.
     *
     * @return $this
     */
    public function withOwner(): static
    {
        return $this->state([
            'user_id' => User::factory()->withPersonalTeam()
        ]);
    }

    /**
     * Create providers using DigitalOceanV2 API.
     *
     * @return $this
     */
    public function digitalOceanV2(): static
    {
        return $this->state([
            'provider' => 'digital_ocean_v2',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
        ]);
    }

    /**
     * Attach this provider to a random user from a collection provided.
     *
     * @return $this
     */
    public function forRandomUserFrom(Collection $users): static
    {
        return $this->afterMaking(fn(Provider $provider) =>
            $this->associateOwner($provider, $users->random())
        );
    }

    /** Attach provider to a user. */
    protected function associateOwner(Provider $provider, User $user): void
    {
        $provider->owner()->associate($user);
    }
}
