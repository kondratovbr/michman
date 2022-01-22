<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Traits\IsSshKey;
use Carbon\CarbonInterface;
use Database\Factories\WorkerSshKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WorkerSshKey Eloquent model
 *
 * Represents an SSH key that our worker process uses to access the server.
 *
 * @property int $id
 * @property string $name
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @property-read Server $server
 *
 * @method static WorkerSshKeyFactory factory(...$parameters)
 */
class WorkerSshKey extends AbstractModel implements SshKeyInterface
{
    use HasFactory,
        IsSshKey;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'external_id',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** Generate a name for a worker SSH key based on the server name. */
    public static function createName(Server|string $server): string
    {
        if ($server instanceof Server)
            $server = $server->name;

        return $server . ' - ' . config('app.name') . ' worker key';
    }

    protected function getSshKeyComment(): string
    {
        return static::createName($this->server);
    }

    protected function getUserAttribute(): User
    {
        return $this->server->user;
    }

    /** Get a relation with the server that uses this key. */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
