<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Server;
use App\Models\ServerSshKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpseclib3\Crypt\EC;

class ServerSshKeyFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = ServerSshKey::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterMaking(function (ServerSshKey $sshKey) {
            if (! isset($sshKey->server))
                return;

            $this->updateKeyAndName($sshKey);
        });
    }

    /**
     * Also create a server owning this server SSH key.
     *
     * @return $this
     */
    public function withServer(): static
    {
        return $this->state([
            'server_id' => Server::factory()->withProvider(),
        ])->afterMaking(function (ServerSshKey $sshKey) {
            $this->updateKeyAndName($sshKey);
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

        return $this->afterMaking(function (ServerSshKey $sshKey) use ($servers) {
            $this->associateWithServer($sshKey, $servers->pop());
        });
    }

    /**
     * Associate an SSH key with a Server.
     */
    protected function associateWithServer(ServerSshKey $sshKey, Server $server): void
    {
        $sshKey->server()->associate($server);
        $this->updateKeyAndName($sshKey);
    }

    /**
     * Create a new SSH key and update "name" attribute of a model.
     */
    protected function updateKeyAndName(ServerSshKey $sshKey): void
    {
        $key = EC::createKey('Ed25519');
        $sshKey->privateKey = $key;
        $sshKey->publicKey = $key->getPublicKey();
        $sshKey->name = ServerSshKey::createName($sshKey->server);
    }
}
