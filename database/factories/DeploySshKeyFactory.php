<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\DeploySshKey;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpseclib3\Crypt\EC;

class DeploySshKeyFactory extends Factory
{
    protected $model = DeploySshKey::class;

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
        return $this->afterMaking(function (DeploySshKey $sshKey) {
            if (! isset($sshKey->project))
                return;

            $this->updateKey($sshKey);
        });
    }

    /**
     * Also create a project owning this deploy key.
     *
     * @return $this
     */
    public function withProject(): static
    {
        return $this->for(Project::factory()->withUserAndServers());
    }

    /**
     * Create SSH keys for a random project from collection,
     * making sure to do it no more than once for every one of them.
     *
     * @return $this
     */
    public function forRandomProjectFromCollectionOnce(Collection $projects): static
    {
        $projects = $projects->shuffle();

        return $this->afterMaking(function (DeploySshKey $sshKey) use ($projects) {
            $this->associateWithProject($sshKey, $projects->pop());
        });
    }

    /** Associate an SSH key with a Server. */
    protected function associateWithProject(DeploySshKey $sshKey, Project $project): void
    {
        $sshKey->project()->associate($project);
        $this->updateKey($sshKey);
    }

    /** Create a new SSH key. */
    protected function updateKey(DeploySshKey $sshKey): void
    {
        $key = EC::createKey('Ed25519');
        $sshKey->privateKey = $key;
        $sshKey->publicKey = $key->getPublicKey();
    }
}
