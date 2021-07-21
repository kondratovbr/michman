<?php declare(strict_types=1);

namespace App\Models;

use App\Casts\Lowercase;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 *
 * @property string $fullDomainName
 *
 * @property-read User $user
 * @property-read Collection $servers
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
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'aliases' => 'array',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];

    /**
     * Get a domain name of this project for the front-end.
     */
    public function getFullDomainNameProperty(): string
    {
        return ($this->allowSubDomains ? '*.' : '') . $this->domain;
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
}
