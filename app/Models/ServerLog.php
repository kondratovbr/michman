<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ServerLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
 * TODO: IMPORTANT! Think about refactoring the server logging system:
 *       Maybe make a model ScriptLog and store a whole log for every ServerScript execution.
 *       Will make it way easier and more reliable to store and retrieve.
 *       Will also give an option to later store the logs in some separate DB more optimized for it.
 *       Mildly related:
 *       need a system for retrieving arbitrary logs from a server.
 *       Like from log files and systemctl for example.
 */

/**
 * Server Log Eloquent model to store SSH logs in the database
 *
 * @property int $id
 * @property int $serverId
 *
 * @property string $type
 * @property string|null $script
 * @property string|null $command
 * @property int|null $exitCode
 * @property string|null $content
 * @property string|null $localFile
 * @property string|null $remoteFile
 * @property bool|null $success
 * @property CarbonInterface $createdAt
 *
 * @property-read bool $renderable
 *
 * @property-read Server $server
 *
 * @method static ServerLogFactory factory(...$parameters)
 */
class ServerLog extends AbstractModel
{
    use HasFactory;

    /**
     * @var string The database connection that should be used by the model.
     *
     * This model uses a separate connection so logs could be created independent
     * of transactions on the main database.
     */
    protected $connection = 'db-logs';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /** @var string[] The attributes that aren't mass assignable. */
    protected $guarded = [];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** Check if this log is of type that can be shown to a user as an output log. */
    protected function getRenderableAttribute(): bool
    {
        return ! empty($this->command) || ! empty($this->content);
    }

    /** Get a relation to the server that owns this log. */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
