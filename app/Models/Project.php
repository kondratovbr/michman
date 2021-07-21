<?php declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
     * Get a relation with the servers this project is using.
     */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'projects_server')
            ->using(ProjectServerPivot::class)
            ->withTimestamps();
    }
}
