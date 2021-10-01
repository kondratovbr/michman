<?php declare(strict_types=1);

namespace App\Models;

use App\Broadcasting\UserChannel;
use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use App\Support\Str;
use BaconQrCode\Renderer\Color\Rgb;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Traits\Notifiable;
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
 * @property string|null $password
 * @property CarbonInterface $emailVerifiedAt
 * @property string $oauthProvider
 * @property string $oauthId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read string $name
 * @property-read string $avatarUrl
 *
 * @property-read Collection $providers
 * @property-read Collection $servers
 * @property-read Collection $userSshKeys
 * @property-read Collection $vcsProviders
 * @property-read Collection $projects
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
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
        'oauth_provider',
        'oauth_id',
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

    public function setEmailAttribute(string $email): void
    {
        $this->attributes['email'] = Str::lower($email);
    }

    /** Derive user's name from the email. */
    public function getNameAttribute(): string
    {
        return explode('@', $this->email, 2)[0];
    }

    /** Get the URL for the user's profile photo. */
    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_photo_url;
    }

    /** Generate a name for the user's automatically created personal team. */
    public function getNameForPersonalTeam(): string
    {
        return ucfirst(explode('@', $this->email, 2)[0]) . "'s Team";
    }

    /** The channel the user receives notification broadcasts on. */
    public function receivesBroadcastNotificationsOn(): string
    {
        return UserChannel::name($this);
    }

    /** Get the 2FA QR code as a more flexible SVG string. */
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

    /** Determine if this user has 2FA enabled. */
    public function tfaEnabled(): bool
    {
        return ! empty($this->two_factor_secret);
    }

    /** Determine if this user authenticates using OAuth. */
    public function usesOauth(): bool
    {
        return ! empty($this->oauthProvider) && ! empty($this->oauthId);
    }

    /** Determine if this user uses email+password authentication. */
    public function usesPassword(): bool
    {
        return ! empty($this->password);
    }

    /**
     * Get this user's preferred locale,
     * which will be used to localize notifications and emails.
     */
    public function preferredLocale(): string
    {
        // TODO: Implement preferredLocale() method.
        return 'en';
    }

    /** Get this user's VcsProvider for a third-party service with the name provided. */
    public function vcs(string $provider, bool $lock = false): VcsProvider|null
    {
        $query = $this->vcsProviders()
            ->where('provider', $provider)
            ->latest();

        if ($lock)
            $query->lockForUpdate();

        /** @var VcsProvider|null $vcsProvider */
        $vcsProvider = $query->first();

        return $vcsProvider;
    }

    /** Get a relation with server providers owned by this user. */
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class);
    }

    /** Get a relation with servers owned by this user. */
    public function servers(): HasManyThrough
    {
        return $this->hasManyThrough(Server::class, Provider::class);
    }

    /** Get a relation with the SSH keys added by this user. */
    public function userSshKeys(): HasMany
    {
        return $this->hasMany(UserSshKey::class);
    }

    /** Get a relation with the VCS providers connected by this user. */
    public function vcsProviders(): HasMany
    {
        return $this->hasMany(VcsProvider::class);
    }

    /** Get a relation with the projects this user owns. */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
