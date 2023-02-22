<?php declare(strict_types=1);

namespace App\Models;

use App\Broadcasting\UserChannel;
use App\Casts\ForceBooleanCast;
use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\HasUuid;
use App\Models\Traits\IsLockable;
use App\Models\Traits\UsesCamelCaseAttributes;
use App\Notifications\VerifyEmailNotification;
use App\Support\Str;
use BaconQrCode\Renderer\Color\Rgb;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as BaseUser;
use App\Models\Traits\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use App\Facades\QrCode;
use Spark\Billable;

/**
 * User Eloquent model
 *
 * @property int $id
 *
 * Properties
 * @property string $email
 * @property string|null $password
 * @property CarbonInterface $emailVerifiedAt
 * @property bool $isDeleting
 * @property array|null $browserEvents
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * Custom attributes
 * @property-read string $name
 * @property-read string $avatarUrl
 *
 * Relations
 * @property-read Collection $providers
 * @property-read Collection $servers
 * @property-read Collection $userSshKeys
 * @property-read Collection $vcsProviders
 * @property-read Collection $projects
 * @property-read Collection $oauthUsers
 * @property-read Collection $webhooks
 *
 * @method static UserFactory factory(...$parameters)
 *
 * @mixin IdeHelperUser
 */
class User extends BaseUser implements MustVerifyEmail, HasLocalePreference
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use UsesCamelCaseAttributes;
    use HasModelHelpers;
    use IsLockable;
    use Billable;
    use HasUuid;

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
        'is_deleting' => ForceBooleanCast::class,
        'browser_events' => 'json',
    ];

    /** @var string[] The accessors to append to the model's array form. */
    protected $appends = [
        'profile_photo_url',
    ];

    protected function setEmailAttribute(string $email): void
    {
        $this->attributes['email'] = Str::lower($email);
    }

    /** Derive user's name from the email. */
    protected function getNameAttribute(): string
    {
        return explode('@', $this->email, 2)[0];
    }

    /** Get the URL for the user's profile photo. */
    protected function getAvatarUrlAttribute(): string
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
        return $this->oauthUsers()->count() > 0;
    }

    /** Determine if this user uses email+password authentication. */
    public function usesPassword(): bool
    {
        return ! empty($this->password);
    }

    /** Get this user's preferred locale, which will be used to localize notifications and emails. */
    public function preferredLocale(): string
    {
        // TODO: IMPORTANT! Implement preferredLocale() method. To do this - store the user's locale and make sure to properly remember and update it when necessary. Same with the dark/light preference, btw.
        return 'en';
    }

    /** Send the customized email verification notification. */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
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

    /**
     * Get the OAuth model for a linked account from a requested provider for this user
     * or null, if the provider isn't linked.
     */
    public function oauth(string|null $provider): OAuthUser|null
    {
        if (is_null($provider)) {
            /** @var OAuthUser $oauth */
            $oauth = $this->oauthUsers()->oldest()->first();
            return $oauth;
        }

        /** @var OAuthUser $oauth */
        $oauth = $this->oauthUsers()->latest()->firstWhere('provider', $provider);
        return $oauth;
    }

    /** Check is this user is an admin. */
    public function isAdmin(): bool
    {
        $adminEmail = config('app.admin_email');

        if (empty($adminEmail)) {
            Log::critical('Admin email is not configured.');
            return false;
        }

        if ($this->isDirty(['email', 'email_verified_at'])) {
            Log::warning('Trying to check isAdmin() on a dirty User model.');
            return false;
        }

        if (empty($this->email))
            return false;

        return $this->email === $adminEmail && $this->hasVerifiedEmail();
    }

    /** Check if an active subscription is required for this user to use all the features. */
    public function subscriptionRequired(): bool
    {
        return (bool) config('app.billing_enabled', true);
    }

    /** Check if this user should have general application features enabled. */
    public function appEnabled(): bool
    {
        return ! $this->subscriptionRequired() || $this->onTrial() || $this->subscribed();
    }

    /** Check if user is currently on the free plan. */
    public function isOnFreePlan(): bool
    {
        return $this->sparkPlan()->options['free'] ?? false;
    }

    /** Store a browser event to send to the front-end. */
    public function addBrowserEvent(string $type, string $name, array|null $payload = null): void
    {
        $events = $this->browserEvents;

        $events[] = [
            'type' => $type,
            'name' => $name,
            'payload' => $payload,
        ];

        $this->browserEvents = $events;
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

    /** Get a relation with the OAuth accounts linked to this user. */
    public function oauthUsers(): HasMany
    {
        return $this->hasMany(OAuthUser::class);
    }

    /** Get a relation with all the webhooks created for projects of this user. */
    public function webhooks(): HasManyThrough
    {
        return $this->hasManyThrough(Webhook::class, Project::class);
    }

    public function scopeIsDeleting(Builder $query): Builder
    {
        return $query->where('is_deleting', true);
    }

    public function scopeNotDeleting(Builder $query): Builder
    {
        return $query
            ->where('is_deleting', false)
            ->orWhereNull('is_deleting');
    }

    public function purge(): bool|null
    {
        $this->providers->each->purge();
        $this->servers->each->purge();
        $this->userSshKeys->each->purge();
        $this->vcsProviders->each->purge();
        $this->projects->each->purge();
        $this->oauthUsers->each->purge();

        return $this->delete();
    }
}
