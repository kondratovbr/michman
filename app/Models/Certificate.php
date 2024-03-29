<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Certificates\CertificateCreatedEvent;
use App\Events\Certificates\CertificateDeletedEvent;
use App\Events\Certificates\CertificateUpdatedEvent;
use App\States\Certificates\CertificateState;
use App\States\Certificates\Installed;
use Carbon\CarbonInterface;
use Database\Factories\CertificateFactory;
use Ds\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ModelStates\HasStates;

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
 *
 * IDs
 * @property int $serverId
 *
 * Properties
 * @property string $type
 * @property string $domain
 * @property CertificateState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Custom attributes
 * @property-read User $user
 * @property-read string $name
 * @property-read string $directory
 * @property-read Set $domains
 *
 * Relations
 * @property-read Server $server
 *
 * @method static CertificateFactory factory(...$parameters)
 *
 * @mixin IdeHelperCertificate
 */
class Certificate extends AbstractModel
{
    use HasFactory;
    use HasStates;

    public const TYPE_LETS_ENCRYPT = 'lets-encrypt';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'type',
        'domain',
        'state',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'state' => CertificateState::class,
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => CertificateCreatedEvent::class,
        'updated' => CertificateUpdatedEvent::class,
        'deleted' => CertificateDeletedEvent::class,
    ];

    /** Get the user who owns this certificate. */
    protected function getUserAttribute(): User
    {
        return $this->server->user;
    }

    /** Derive a name for this certificate from its domain. */
    protected function getNameAttribute(): string
    {
        return $this->domain;
    }

    /** Get a directory where the files related to this certificate are stored on a server. */
    protected function getDirectoryAttribute(): string
    {
        return "/etc/letsencrypt/live/$this->name";
    }

    /** Get the certificate domain wrapped in a set for compatibility with some unrefactored code. */
    protected function getDomainsAttribute(): Set
    {
        return new Set([$this->domain]);
    }

    /** Check if this certificate has a domain from a project. */
    public function hasDomainOf(Project $project): bool
    {
        return $this->domains->contains($project->domain)
            || ! $this->domains->intersect($project->aliases)->isEmpty();
    }

    /** Check if this certificate is a subset of another certificate. */
    public function isSubsetOf(Certificate $certificate): bool
    {
        return $this->domains->diff($certificate->domains)->isEmpty();
    }

    /** Check if this certificate is installed on a server. */
    public function isInstalled(): bool
    {
        return $this->state->is(Installed::class);
    }

    /** Get a relation to the server that has this certificate installed. */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
