<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use BaconQrCode\Renderer\Color\Rgb;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\HtmlString;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use App\Facades\QrCode;

/**
 * User Eloquent model
 *
 * @property int $id
 * @property string $email
 * @property CarbonInterface $emailVerifiedAt
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $name
 * @property-read string $avatarUrl
 *
 * @property-read Collection $providers
 * @property-read Collection $servers
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
        TwoFactorAuthenticatable,
        UsesCamelCaseAttributes,
        HasModelHelpers;

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

    /**
     * Get the URL for the user's profile photo.
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_photo_url;
    }

    /**
     * Generate a name for the user's automatically created personal team.
     */
    public function getNameForPersonalTeam(): string
    {
        return ucfirst(explode('@', $this->email, 2)[0]) . "'s Team";
    }

    /**
     * Get the 2FA QR code as a more flexible SVG string.
     */
    public function twoFactorQrCodeSvg(
        int $size = 192,
        int $margin = 0,
        Rgb $background = null,
        Rgb $color = null
    ): HtmlString|string {
        $background ??= new Rgb(255, 255, 255);
        $color ??= new Rgb(0, 0, 0);

        return QrCode::format('svg')
            ->size($size)
            ->margin($margin)
            ->errorCorrection('M')
            ->backgroundColor($background->getRed(), $background->getGreen(), $background->getBlue())
            ->color($color->getRed(), $color->getGreen(), $color->getBlue())
            ->generate($this->twoFactorQrCodeUrl());
    }

    /**
     * Get a relation with server providers owned by this user.
     */
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class);
    }

    /**
     * Get a relation with servers owned by this user.
     */
    public function servers(): HasManyThrough
    {
        // TODO: CRITICAL! Does it work? Need to declare foreign keys here?
        return $this->hasManyThrough(Server::class, Provider::class);
    }
}
