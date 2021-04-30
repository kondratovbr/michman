<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Ssh\Ssh;

/**
 * Server Eloquent model
 *
 * @property int $id
 * @property string $externalId
 * @property string $name
 * @property string $type
 * @property string $publicIp
 * @property string $sshPort
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 *
 * @property-read Provider $provider
 * @property-read WorkerSshKey $workerSshKey
 *
 * @method static ProviderFactory factory(...$parameters)
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
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [];

    /**
     * Open an SSH session to the server.
     */
    public function ssh(string $user): Ssh
    {
        $ssh = Ssh::create($user, $this->publicIp)
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
