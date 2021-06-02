<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Database Eloquent model
 *
 * @property int $id
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Server $server
 * @property-read Collection $databaseUsers
 */
class Database extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /**
     * Get a relation with the server that holds this database.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get a relation with the database users that have access to this database.
     */
    public function databaseUsers(): BelongsToMany
    {
        return $this->belongsToMany(DatabaseUser::class, 'database_database_users')
            ->withTimestamps();
    }
}