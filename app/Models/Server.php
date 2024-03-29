<?php declare(strict_types=1);

namespace App\Models;

use App\Events\Servers\ServerCreatedEvent;
use App\Events\Servers\ServerDeletedEvent;
use App\Events\Servers\ServerUpdatedEvent;
use App\Exceptions\SshAuthFailedException;
use App\States\Servers\Ready;
use App\States\Servers\ServerState;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\ServerFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use RuntimeException;
use Spatie\ModelStates\HasStates;

/**
 * Server Eloquent model
 *
 * @property int $id
 *
 * IDs
 * @property int $providerId
 *
 * Properties
 * @property string $externalId
 * @property string $region
 * @property string $size
 * @property string $name
 * @property string $type
 * @property string|null $publicIp
 * @property string $sshPort
 * @property string|null $sshHostKey
 * @property string|null $sudoPassword
 * @property bool|null $suitable
 * @property bool|null $available
 * @property string|null $installedDatabase
 * @property string|null $databaseRootPassword
 * @property string|null $installedCache
 * @property ServerState $state
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 *
 * Custom attributes
 * @property-read User $user
 * @property-read string $publicWorkerDir
 *
 * Relations
 * @property-read Provider $provider
 * @property-read WorkerSshKey $workerSshKey
 * @property-read Collection $logs
 * @property-read Collection $userSshKeys
 * @property-read Collection $databases
 * @property-read Collection $databaseUsers
 * @property-read Collection $pythons
 * @property-read ServerSshKey $serverSshKey
 * @property-read Collection $firewallRules
 * @property-read Collection $projects
 * @property-read Collection $deployments
 * @property-read DeploymentServerPivot|null $serverDeployment
 * @property-read Collection $certificates
 * @property-read Collection $workers
 * @property-read Collection $daemons
 *
 * @method static ServerFactory factory(...$parameters)
 *
 * @mixin IdeHelperServer
 */
class Server extends AbstractModel
{
    use HasFactory;
    use HasStates;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'type',
        'external_id',
        'region',
        'size',
        'public_ip',
        'ssh_port',
        'ssh_host_key',
        'sudo_password',
        'suitable',
        'available',
        'database_root_password',
        'state',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'state' => ServerState::class,
        'suitable' => 'boolean',
        'available' => 'boolean',
        'sudo_password' => 'encrypted',
        'database_root_password' => 'encrypted',
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        'created' => ServerCreatedEvent::class,
        'updated' => ServerUpdatedEvent::class,
        'deleted' => ServerDeletedEvent::class,
    ];

    /** Get SSH port attribute or the default one if it's null. */
    protected function getSshPortAttribute(): string
    {
        return $this->attributes['ssh_port'] ?? (string) config('servers.default_ssh_port');
    }

    /** Get the owner of this server. */
    protected function getUserAttribute(): User
    {
        return $this->provider->user;
    }

    /** Get the path to a directory for public files on this server. */
    protected function getPublicWorkerDirAttribute(): string
    {
        return '/home/' . config('servers.worker_user') . '/public';
    }

    /** Check if a database can be created on this server. */
    public function canCreateDatabase(): bool
    {
        return ! empty($this->installedDatabase);
    }

    /** Check if a database user can be created on this server. */
    public function canCreateDatabaseUser(): bool
    {
        return ! empty($this->installedDatabase);
    }

    // TODO: Do I even use this?
    /** Check if this server has an SSL certificate. */
    public function hasSsl(): bool
    {
        return $this->certificates()->count() > 0;
    }

    /** Get a short info about this server to show in the UI. */
    public function shortInfo(): string
    {
        $info = [];

        if (! $this->pythons->isEmpty()) {
            $info[] = 'Python ' .
                __('servers.pythons.versions.' . $this->pythons->firstWhereMax('version')->version);
        }

        if (! empty($this->installedDatabase))
            $info[] = __("servers.databases.$this->installedDatabase");

        if (! empty($this->installedCache))
            $info[] = __("servers.caches.$this->installedCache");

        return implode(', ', $info);
    }

    /**
     * Filter the certificates installed on this server
     * for the ones that should be used for a project provided based on their domains.
     */
    public function getCertificatesFor(Project $project): Collection
    {
        return $this->certificates->filter(fn(Certificate $cert) =>
            $cert->hasDomainOf($project)
        );
    }

    public function hasActiveCertificateForDomain(string $domain): bool
    {
        $certs = $this->certificates->filter(fn(Certificate $cert) => $cert->isInstalled());

        /** @var Certificate $cert */
        foreach ($certs as $cert) {
            if ($cert->domains->contains($domain))
                return true;
        }

        return false;
    }

    /** Check if this server was prepared and ready to be managed by the user. */
    public function isReady(): bool
    {
        return $this->state->is(Ready::class);
    }

    /** Open an SSH session to the server with SFTP enabled. */
    public function sftp(string $user = null): SFTP
    {
        /** @var SFTP $sftp */
        $sftp = $this->ssh($user, $this->newSftpSession());
        return $sftp;
    }

    /** Open an SSH session to the server. */
    public function ssh(string $user = null, SSH2 $ssh = null): SSH2
    {
        $ssh ??= $this->newSshSession();
        $user ??= 'root';

        if (! isset($this->sshHostKey))
            $this->updateSshHostKey($ssh);

        // TODO: IMPORTANT! Figure out what to do if the host key verification fails. Read about why it may happen in normal operation. Probably will have to notify the user and then ask them to confirm that everything is OK.
        if ($ssh->getServerPublicHostKey() != $this->sshHostKey)
            throw new RuntimeException('Host key verification failed.');

        if (! $ssh->login($user, $this->workerSshKey->privateKey))
            throw new SshAuthFailedException('Key authentication failed.');

        $ssh->setKeepAlive(1);

        return $ssh;
    }

    /** Load and save the server's SSH host key. */
    protected function updateSshHostKey(SSH2 $ssh = null): void
    {
        $ssh ??= $this->newSshSession();

        $hostKey = $ssh->getServerPublicHostKey();

        // SSH2::getServerPublicHostKey() may return false if the key received wasn't signed correctly.
        if ($hostKey === false)
            throw new RuntimeException('Server\'s SSH host key wasn\'t received correctly.');

        $this->update(['ssh_host_key' => $hostKey]);
    }

    /** Create a new instance of an SSH session to this server. */
    protected function newSshSession(): SSH2
    {
        return App::make(SSH2::class, [
            'host' => $this->publicIp,
            'port' => $this->sshPort,
        ]);
    }

    /** Create a new instance of an SFTP session to this server. */
    protected function newSftpSession(): SFTP
    {
        return App::make(SFTP::class, [
            'host' => $this->publicIp,
            'port' => $this->sshPort,
        ]);
    }

    /** Get this server's logs between the timestamps. */
    public function getLogs(CarbonInterface|string $from, CarbonInterface|string $to): Collection
    {
        if (is_string($from))
            $from = new CarbonImmutable($from);

        if (is_string($to))
            $to = new CarbonImmutable($to);

        return $this->logs()
            ->whereBetween('created_at', [$from, $to])
            ->oldest()
            ->get();
    }

    /** Get a relation to the provider that runs this server. */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /** Get a relation to the SSH key used to access this server. */
    public function workerSshKey(): HasOne
    {
        return $this->hasOne(WorkerSshKey::class);
    }

    /** Get a relation to the SSH keys added by the user for this server. */
    public function userSshKeys(): BelongsToMany
    {
        return $this->belongsToMany(UserSshKey::class, 'server_user_ssh_key')
            ->using(ServerUserSshKeyPivot::class)
            ->withTimestamps();
    }

    /** Get a relation to the logs of operations performed on this server. */
    public function logs(): HasMany
    {
        return $this->hasMany(ServerLog::class);
    }

    /** Get a relation with the databases this server holds. */
    public function databases(): HasMany
    {
        return $this->hasMany(Database::class);
    }

    /** Get a relation with the database users this server has. */
    public function databaseUsers(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }

    /** Get a relation with Python instances installed on this server. */
    public function pythons(): HasMany
    {
        return $this->hasMany(Python::class);
    }

    /** Get a relation with the SHH key that this server is using to access VCS repositories. */
    public function serverSshKey(): HasOne
    {
        return $this->hasOne(ServerSshKey::class);
    }

    /** Get a relation with the firewall rules created for this server. */
    public function firewallRules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }

    /** Get a relation with the projects that are using this server. */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_server')
            ->using(ProjectServerPivot::class)
            ->withPivot(ProjectServerPivot::$pivotAttributes)
            ->withTimestamps();
    }

    /** Get a relation with the deployments performed on this server. */
    public function deployments(): BelongsToMany
    {
        return $this->belongsToMany(Deployment::class, 'deployment_server')
            ->as(DeploymentServerPivot::ACCESSOR)
            ->using(DeploymentServerPivot::class)
            ->withPivot(DeploymentServerPivot::$pivotAttributes)
            ->withTimestamps();
    }

    /** Get a relation with the SSL certificates installed on this server. */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /** Get a relation with the queue workers that run on this server. */
    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    /** Get a relation with the daemons that run on this server. */
    public function daemons(): HasMany
    {
        return $this->hasMany(Daemon::class);
    }

    /*
     * TODO: IMPORTANT! Cover all these custom purge() methods with tests.
     */
    public function purge(): bool|null
    {
        $this->userSshKeys()->detach();
        $this->deployments()->detach();

        $this->workerSshKey?->purge();
        $this->logs->each->purge();
        $this->databases->each->purge();
        $this->databaseUsers->each->purge();
        $this->pythons->each->purge();
        $this->serverSshKey?->purge();
        $this->firewallRules->each->purge();

        $this->projects->each(function (Project $project) {
            $project->servers()->detach($this->getKey());

            if ($project->servers()->count() == 0)
                $project->purge();
        });

        $this->certificates->each->purge();
        $this->workers->each->purge();
        $this->daemons->each->purge();

        return $this->delete();
    }
}
