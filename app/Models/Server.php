<?php declare(strict_types=1);

namespace App\Models;

use App\Exceptions\SshAuthFailedException;
use Carbon\CarbonInterface;
use Database\Factories\ServerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use phpseclib3\Net\SSH2;

/**
 * Server Eloquent model
 *
 * @property int $id
 * @property string $externalId
 * @property string $name
 * @property string $type
 * @property string|null $publicIp
 * @property string|null $sshPort
 * @property string|null $sshHostKey
 * @property bool|null $suitable
 * @property bool|null $available
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 *
 * @property-read Provider $provider
 * @property-read WorkerSshKey $workerSshKey
 *
 * @method static ServerFactory factory(...$parameters)
 */
class Server extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'type',
        'external_id',
        'public_ip',
        'ssh_port',
        'ssh_host_key',
        'suitable',
        'available',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'suitable' => 'boolean',
        'available' => 'boolean',
    ];

    /**
     * Open an SSH session to the server.
     */
    public function ssh(string $user = null): SSH2
    {
        $ssh = $this->newSshSession();

        if (! isset($this->sshHostKey))
            $this->updateSshHostKey($ssh);

        if ($ssh->getServerPublicHostKey() != $this->sshHostKey)
            throw new \RuntimeException('Host key verification failed.');

        $user ??= (string) config('servers.worker_user');

        if (! $ssh->login($user, $this->workerSshKey->privateKey))
            throw new SshAuthFailedException('Key authentication failed.');

        return $ssh;
    }

    /**
     * Load and save the server's SSH host key.
     */
    protected function updateSshHostKey(SSH2 $ssh = null): void
    {
        $ssh ??= $this->newSshSession();

        $hostKey = $ssh->getServerPublicHostKey();

        // SSH2::getServerPublicHostKey() may return false if the key received wasn't signed correctly.
        if ($hostKey === false)
            throw new \RuntimeException('Server\'s SSH host key wasn\'t received correctly.');

        $this->update(['ssh_host_key' => $hostKey]);
    }

    protected function newSshSession(): SSH2
    {
        return new SSH2(
            $this->publicIp,
            $this->sshPort ?? (string) config('servers.default_ssh_port'),
        );
    }

    /**
     * Get a relation to the provider that runs this server.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get a relation to the SSH key used to access this server.
     */
    public function workerSshKey(): HasOne
    {
        return $this->hasOne(WorkerSshKey::class);
    }
}
