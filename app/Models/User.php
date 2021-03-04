<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

/**
 * User Eloquent model
 *
 * @property int $id
 * @property string $email
 * @property CarbonInterface $email_verified_at
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 *
 * @property-read string $name
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        HasProfilePhoto,
        HasTeams,
        Notifiable,
        TwoFactorAuthenticatable;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'email',
        'password',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** @var string[] The accessors to append to the model's array form. */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Derive user's name from the email.
     */
    public function getNameAttribute(): string
    {
        return explode('@', $this->email, 2)[0];
    }
}
