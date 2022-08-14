<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
use App\Support\Str;
use Carbon\CarbonInterface;
use Database\Factories\DeploySshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeploySshKey Eloquent model
 *
 * Represents an SSH key that was automatically created for a server
 * and added to a specific VCS repository to be used for deployment.
 *
 * @property int $id
 *
 * IDs
 * @property int $projectId
 *
 * Properties
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Custom attributes
 * @property-read string $name
 *
 * Relations
 * @property-read Project $project
 *
 * @method static DeploySshKeyFactory factory(...$parameters)
 *
 * @mixin IdeHelperDeploySshKey
 */
class DeploySshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;
    use IsSshKey;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        //
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** Get a name for the key to use as a filename on servers. */
    protected function getNameAttribute(): string
    {
        return Str::snake(Str::lower($this->project->domain));
    }

    protected function getSshKeyComment(): string
    {
        return $this->project->domain . ' - deploy key';
    }

    /** Get a relation with the project that uses this key. */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
