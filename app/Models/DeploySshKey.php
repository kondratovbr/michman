<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
use Carbon\CarbonInterface;
use Database\Factories\DeploySshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Crypt\Common\PrivateKey as PrivateKeyInterface;
use phpseclib3\Crypt\Common\PublicKey as PublicKeyInterface;
use phpseclib3\Crypt\PublicKeyLoader;

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
 * @property-read Server $server
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
        return $this->server->name . ' - deploy key';
    }

    // TODO: CRITICAL! This is a mistake. A deploy key should be owned by a project and attached to many servers - all of the servers where the project is deployed. Fix and don't forget the migrations and the inverse part of this relation. The comment in keyToString is also not correct - should include project name.
    /**
     * Get a relation with the server that has this key.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
