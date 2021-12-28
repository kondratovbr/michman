<?php declare(strict_types=1);

namespace App\Models;

use App\Casts\SetCast;
use App\Events\Projects\ProjectCreatedEvent;
use App\Events\Projects\ProjectDeletedEvent;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Support\Str;
use Database\Factories\ProjectFactory;
use Ds\Set;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Project Eloquent model
 *
 * @property int $id
 * @property string $domain
 * @property Set<string> $aliases
 * @property bool $allowSubDomains
 * @property string $type
 * @property string $root
 * @property string|null $pythonVersion
 * @property string|null $repo
 * @property string|null $branch
 * @property string|null $package
 * @property bool|null $useDeployKey
 * @property string|null $requirementsFile
 * @property string|null $environment
 * @property string|null $deployScript
 * @property string|null $gunicornConfig
 * @property string|null $nginxConfig
 *
 * @property-read string $fullDomainName
 * @property-read string $serverUsername
 * @property-read string|null $projectName
 * @property-read string $deployScriptFilePath
 * @property-read string $envFilePath
 * @property-read string $nginxConfigFilePath
 * @property-read string $userNginxConfigFilePath
 * @property-read string $gunicornConfigFilePath
 * @property-read string $projectDir
 * @property-read string $michmanDir
 * @property-read bool $deployed
 * @property-read bool $webhookEnabled
 * @property-read string|null $repoUrl
 * @property-read string|null $vcsProviderName
 *
 * @property-read User $user
 * @property-read Collection $servers
 * @property-read DeploySshKey|null $deploySshKey
 * @property-read VcsProvider|null $vcsProvider
 * @property-read Database|null $database
 * @property-read DatabaseUser|null $databaseUser
 * @property-read Collection $deployments
 * @property-read Collection $workers
 * @property-read Webhook|null $webhook
 *
 * @method static ProjectFactory factory(...$parameters)
 */
class Project extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'domain',
        'aliases',
        'allow_sub_domains',
        'type',
        'root',
        'python_version',
        'vcs_provider',
        'repo',
        'branch',
        'package',
        'use_deploy_key',
        'requirements_file',
        'environment',
        'deploy_script',
        'gunicorn_config',
        'nginx_config',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'aliases' => SetCast::class,
        'use_deploy_key' => 'bool',
        'environment' => 'encrypted',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => ProjectCreatedEvent::class,
        'updated' => ProjectUpdatedEvent::class,
        'deleted' => ProjectDeletedEvent::class,
    ];

    /** Get a domain name of this project for the front-end. */
    public function getFullDomainNameAttribute(): string
    {
        return ($this->allowSubDomains ? '*.' : '') . $this->domain;
    }

    /** Get a name for a server user that will be created and used to run this project. */
    public function getServerUsernameAttribute(): string
    {
        return Str::replace('.', '_', Str::lower($this->domain));
    }

    /** Derive a project name from the repo name of this project. */
    public function getProjectNameAttribute(): string|null
    {
        if (empty($this->repo))
            return null;

        return explode('/', $this->repo, 2)[1];
    }

    /** Get the path to the file where the deploy script is stored on a server. */
    public function getDeployScriptFilePathAttribute(): string
    {
        return "/home/{$this->serverUsername}/.michman/{$this->projectName}_deploy.sh";
    }

    /** Get the path to the .env file on a server. */
    public function getEnvFilePathAttribute(): string
    {
        return "/home/{$this->serverUsername}/{$this->domain}/.env";
    }

    /** Get the path to the Nginx config file on a server. */
    public function getNginxConfigFilePathAttribute(): string
    {
        return "/etc/nginx/sites-available/{$this->projectName}.conf";
    }

    /** Get the path to the user-customizable part of the Nginx config on a server. */
    public function getUserNginxConfigFilePathAttribute(): string
    {
        return "{$this->michmanDir}/{$this->projectName}_nginx.conf";
    }

    /** Get the path to the Gunicorn config file on a server. */
    public function getGunicornConfigFilePathAttribute(): string
    {
        return "{$this->michmanDir}/{$this->projectName}_gunicorn_config.py";
    }

    /** Get the path to the directory where this project is cloned on a server. */
    public function getProjectDirAttribute(): string
    {
        return "/home/{$this->serverUsername}/{$this->domain}";
    }

    /** Get the path to the directory where we store important files we add to a server. */
    public function getMichmanDirAttribute(): string
    {
        return "/home/{$this->serverUsername}/.michman";
    }

    /** Check if this project is currently deployed. */
    public function getDeployedProperty(): bool
    {
        // TODO: CRITICAL! Update this when "undeploy" feature is implemented,
        //       because it will be incorrect.
        return ! is_null($this->getCurrentDeployment());
    }

    /** Check if this project has webhook enabled. */
    public function getWebhookEnabledProperty(): bool
    {
        if (! isset($this->webhook))
            return false;

        return $this->webhook->isEnabled();
    }

    /** Check if the project has a configured Git repository. */
    public function repoInstalled(): bool
    {
        return isset($this->vcsProvider) && ! empty($this->repo);
    }

    /** Get the latest triggered deployment of this project. */
    public function getLatestDeployment(): Deployment|null
    {
        return $this->deployments()->latest()->first();
    }
    
    /** Get the latest successful deployment of this project. */
    public function getCurrentDeployment(): Deployment|null
    {
        return $this->deployments()->successful()->latest()->first();
    }

    /** Get a public URL to the repository configured for this project. */
    public function getRepoUrlAttribute(): string|null
    {
        if (empty($this->repo) || ! isset($this->vcsProvider))
            return null;

        // TODO: Creating API instance here may be slow because of token refreshing. Should probably move the URL generation logic.
        return $this->vcsProvider->api()->repoUrl($this->repo);
    }

    /** Get a printable name of a VCS service configured for this project. */
    public function getVcsProviderNameAttribute(): string|null
    {
        if (! isset($this->vcsProvider))
            return null;

        return __("projects.repo.providers.{$this->vcsProvider->provider}");
    }

    /** Get a short info about this project to show in the UI. */
    public function shortInfo(): string
    {
        $info = [];

        $info[] = __("projects.types.{$this->type}");

        if (! empty($this->pythonVersion))
            $info[] = 'Python ' . __("servers.pythons.versions.{$this->pythonVersion}");

        return implode(', ', $info);
    }

    /** Get a relation with the user that owns this project. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get a relation with the servers this project is using. */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'project_server')
            ->using(ProjectServerPivot::class)
            ->withTimestamps();
    }

    /** Get a relation with the deploy key used by this project, if any. */
    public function deploySshKey(): HasOne
    {
        return $this->hasOne(DeploySshKey::class);
    }

    /** Get a relation to the VCS provider this project uses, if any. */
    public function vcsProvider(): BelongsTo
    {
        return $this->belongsTo(VcsProvider::class);
    }

    /** Get a relation to the deployments performed for this project, if any. */
    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /** Get a relation with the database that this project is using, if any. */
    public function database(): BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    /** Get a relation with the database user that this project is using, if any. */
    public function databaseUser(): BelongsTo
    {
        return $this->belongsTo(DatabaseUser::class);
    }

    /** Get a relation with the queue workers configured for this project. */
    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    /** Get a relation with the webhook that is triggered when this project has a new commit pushed in the repo. */
    public function webhook(): HasOne
    {
        return $this->hasOne(Webhook::class);
    }
}
