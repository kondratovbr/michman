<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
use Carbon\CarbonInterface;
use Database\Factories\ServerSshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ServerSshKey Eloquent model
 *
 * Represents an SSH key that is used by a server
 * to access project repositories during deployment.
 *
 * @property int $id
 * @property int $serverId
 *
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 * @property-read ServerSshKeyVcsProviderPivot|null $vcsProviderKey
 *
 * @method static ServerSshKeyFactory factory(...$parameters)
 */
class ServerSshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory;
    use IsSshKey;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        //
    ];

    protected function getSshKeyComment(): string
    {
        return static::createName($this->server);
    }

    /** Get a name for a server SSH key based on the server name. */
    public static function createName(Server|string $server): string
    {
        if ($server instanceof Server)
            $server = $server->name;

        return $server . ' - server key';
    }

    /** Get a relation with the server that uses this key. */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /** Get a relation with the VcsProviders that have this key added. */
    public function vcsProviders(): BelongsToMany
    {
        return $this->belongsToMany(VcsProvider::class, 'server_ssh_key_vcs_provider')
            ->as(ServerSshKeyVcsProviderPivot::ACCESSOR)
            ->using(ServerSshKeyVcsProviderPivot::class)
            ->withPivot(ServerSshKeyVcsProviderPivot::$pivotAttributes)
            ->withTimestamps();
    }
}
