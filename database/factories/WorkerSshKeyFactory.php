<?php declare(strict_types=1);

namespace Database\Factories;

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
        $key = EC::createKey('Ed25519');

        // TODO: CRITICAL! CONTINUE! Doesn't work - tries to access ->server->name when there's no server assigned yet.

        return [
            'private_key' => $key,
            'public_key' => $key->getPublicKey(),
            'name' => $this->faker->domainName,
            'external_id' => null,
        ];
    }
}
