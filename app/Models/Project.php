<?php declare(strict_types=1);

namespace App\Models;

use App\Casts\ForceBooleanCast;
use App\Events\Projects\ProjectCreatedEvent;
use App\Events\Projects\ProjectDeletedEvent;
use App\Events\Projects\ProjectUpdatedEvent;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Project Eloquent model
 *
 * @property int $id
 * @property string $domain
 * @property string[] $aliases
 * @property bool $allowSubDomains
 * @property string $type
 * @property string $root
 * @property string|null $pythonVersion
 * @property string|null $repo
 * @property string|null $branch
 * @property bool $useDeployKey
 *
 * @property string $fullDomainName
 *
 * @property-read User $user
 * @property-read Collection $servers
 * @property-read DeploySshKey|null $deploySshKey
 * @property-read VcsProvider|null $vcsProvider
 *
 * @method static ProjectFactory factory(...$parameters)
 */
class Project extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'domain',
        'aliases',
        'allow_sub_domains',
        'type',
        'root',
        'python_version',
        'vcs_provider',
        'repo',
        'branch',
        'use_deploy_key',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'aliases' => 'array',
        'use_deploy_key' => ForceBooleanCast::class,
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => ProjectCreatedEvent::class,
        'updated' => ProjectUpdatedEvent::class,
        'deleted' => ProjectDeletedEvent::class,
    ];

    /**
     * Get a domain name of this project for the front-end.
     */
    public function getFullDomainNameAttribute(): string
    {
        return ($this->allowSubDomains ? '*.' : '') . $this->domain;
    }

    /**
     * Check if the project has a configured Git repository.
     */
    public function repoInstalled(): bool
    {
        return isset($this->vcsProvider) && ! empty($this->repo);
    }

    /**
     * Get a relation with the user that owns this project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation with the servers this project is using.
     */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'project_server')
            ->using(ProjectServerPivot::class)
            ->withTimestamps();
    }

    /**
     * Get a relation with the deploy key used by this project, if any.
     */
    public function deploySshKey(): HasOne
    {
        return $this->hasOne(DeploySshKey::class);
    }

    /**
     * Get a relation to the VCS provider this project uses, if any.
     */
    public function vcsProvider(): BelongsTo
    {
        return $this->belongsTo(VcsProvider::class);
    }
}
