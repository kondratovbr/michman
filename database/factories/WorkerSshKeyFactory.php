<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Database\Eloquent\Collection;
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
            'external_id' => null,
        ];
    }

    /**
     * Also create a server owning this worker SSH key.
     *
     * @return $this
     */
    public function withServer(): static
    {
        return $this->state([
            'server_id' => Server::factory()->withProvider(),
        ])->afterMaking(function (WorkerSshKey $sshKey) {
            $this->updateKeyAndName($sshKey);
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (WorkerSshKey $sshKey) {
            if (! isset($sshKey->server))
                return;

            $this->updateKeyAndName($sshKey);
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

    /**
     * Create SSH keys for a random server from collection,
     * making sure to do it no more than once for every one of them.
     *
     * @return $this
     */
    public function forRandomServerFromCollectionOnce(Collection $servers): static
    {
        $servers = $servers->shuffle();

        return $this->afterMaking(function (WorkerSshKey $sshKey) use ($servers) {
            $this->associateWithServer($sshKey, $servers->pop());
        });
    }

    /**
     * Associate an SSH key with a Server.
     */
    protected function associateWithServer(WorkerSshKey $sshKey, Server $server): void
    {
        $sshKey->server()->associate($server);
        $this->updateKeyAndName($sshKey);
    }

    /**
     * Create a new SSH key and update "name" attribute of a model.
     */
    protected function updateKeyAndName(WorkerSshKey $sshKey): void
    {
        $key = EC::createKey('Ed25519');
        $sshKey->privateKey = $key;
        $sshKey->publicKey = $key->getPublicKey();
        $sshKey->name = WorkerSshKey::createName($sshKey->server);
    }
}
