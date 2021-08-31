<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Certificates\CertificateCreatedEvent;
use App\Events\Certificates\CertificateDeletedEvent;
use App\Events\Certificates\CertificateUpdatedEvent;
use Carbon\CarbonInterface;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// TODO: CRITICAL! CONTINUE. Build front-end for Let's Encrypt, update deployment logic (different Nginx config) and test everything.

/*
 * To receive a certificate:
 * certbot certonly -n -m USER_EMAIL --agree-tos -d DOMAINS --cert-name CERT_NAME --webroot --webroot-path WEBROOT_PATH
 *     -n - non-interactively
 *     --webroot --webroot-path WEBROOT_PATH - put an ACME challenge files into this directory and attempt to request them from the outside
 *
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
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Project $project
 *
 * @method static CertificateFactory factory(...$parameters)
 */
class Certificate extends AbstractModel
{
    use HasFactory;

    public const TYPE_LETS_ENCRYPT = 'lets-encrypt';

    public const STATUS_INSTALLED = 'installed';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'type',
        'domains',
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
     * Get a relation to the servers that has this certificate installed.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
