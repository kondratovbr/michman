<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\OAuthUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// TODO: CRITICAL! CONTINUE.

/**
 * OAuthUser Eloquent model
 *
 * Represents an OAuth account linked to some User model.
 *
 * @property int $id
 * @property string $provider
 * @property string $oauthId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read User $user
 *
 * @method static OAuthUserFactory factory(...$parameters)
 */
class OAuthUser extends AbstractModel
{
    use HasFactory;

    /** @var string The database table associated with the model. */
    protected $table = 'oauth_users';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'provider',
        'oauth_id',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];

    /** Get a user this OAuth account if linked to. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
