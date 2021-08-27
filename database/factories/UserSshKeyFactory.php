<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSshKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpseclib3\Crypt\EC;

class UserSshKeyFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = UserSshKey::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (UserSshKey $sshKey) {
            $sshKey->publicKey = EC::createKey('Ed25519')->getPublicKey();;
        });
    }

    /**
     * Also create a user owning this key.
     *
     * @return $this
     */
    public function withUser(): static
    {
        return $this->for(User::factory()->withPersonalTeam());
    }
}
