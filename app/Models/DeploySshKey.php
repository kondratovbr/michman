<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
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
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Project $project
 *
 * @method static DeploySshKeyFactory factory(...$parameters)
 */
class DeploySshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory,
        IsSshKey;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        //
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    protected function getSshKeyComment(): string
    {
        return $this->project->domain . ' - deploy key';
    }

    /**
     * Get a relation with the project that uses this key.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
