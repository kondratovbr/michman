<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Provider;
use App\Models\User;
use App\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = Provider::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // TODO: Seed some others as well.
            'provider' => 'digital_ocean_v2',
            'token' => Str::random(32),
            'key' => null,
            'secret' => null,
            'name' => $this->faker->domainName,
            'ssh_key_added' => null,
        ];
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

    /**
     * Attach provider to a user.
     */
    protected function associateOwner(Provider $provider, User $user): void
    {
        $provider->owner()->associate($user);
    }
}
