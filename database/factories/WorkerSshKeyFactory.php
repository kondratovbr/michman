<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpseclib3\Crypt\EC;

class WorkerSshKeyFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = WorkerSshKey::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'server_id' => Server::factory(),
            'external_id' => null,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (WorkerSshKey $sshKey) {
            $key = EC::createKey('Ed25519');
            $sshKey->privateKey = $key;
            $sshKey->publicKey = $key->getPublicKey();
            $sshKey->name = $sshKey->server->name ?? $this->faker->domainName;
        });
    }

    /**
     * Add a random external_id attribute.
     *
     * @return $this
     */
    public function withRandomExternalId(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'external_id' => rand(1, 10000),
            ];
        });
    }
}
