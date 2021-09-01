<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Certificates\CertificateCreatedEvent;
use App\Events\Certificates\CertificateDeletedEvent;
use App\Events\Certificates\CertificateUpdatedEvent;
use Carbon\CarbonInterface;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
 * To "expand" a certificate, i.e. to add more sub-domains:
 * certbot certonly --expand -d example.com -d www.example.com -d shop.example.com
 *
 * TODO: Try to run this one non-interactively.
 * certbot delete --cert-name CERT_NAME
 */

/**
 * Certificate Eloquent model
 *
 * @property int $id
 * @property string $type
 * @property string[] $domains
 * @property string|null $status
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 * @property-read string $name
 * @property-read string $directory
 *
 * @property-read Server $server
 *
 * @method static CertificateFactory factory(...$parameters)
 */
class Certificate extends AbstractModel
{
    use HasFactory;

    public const TYPE_LETS_ENCRYPT = 'lets-encrypt';

    public const STATUS_INSTALLING = 'installing';
    public const STATUS_INSTALLED = 'installed';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'type',
        'domains',
        'status',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'domains' => 'array',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => CertificateCreatedEvent::class,
        'updated' => CertificateUpdatedEvent::class,
        'deleted' => CertificateDeletedEvent::class,
    ];

    /**
     * Get the user who owns this certificate.
     */
    public function getUserAttribute(): User
    {
        return $this->project->user;
    }

    /**
     * Derive a name for this certificate from its domains.
     */
    public function getNameAttribute(): string
    {
        return $this->domains[0];
    }

    /**
     * Get a directory where the files related to this certificate are stored on a server.
     */
    public function getDirectoryAttribute(): string
    {
        return "/etc/letsencrypt/live/{$this->name}";
    }

    /**
     * Check if this certificate is a subset of another certificate.
     */
    public function isSubsetOf(Certificate $certificate): bool
    {
        return empty(array_diff($this->domains, $certificate->domains));
    }

    /**
     * Check if this certificate is installed on a server.
     */
    public function isInstalled(): bool
    {
        return $this->status === static::STATUS_INSTALLED;
    }

    /**
     * Get a relation to the server that has this certificate installed.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
