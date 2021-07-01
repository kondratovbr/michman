<?php declare(strict_types=1);

namespace App\Models;

use App\Exceptions\SshAuthFailedException;
use Carbon\CarbonInterface;
use Database\Factories\ServerFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;

/**
 * Server Eloquent model
 *
 * @property int $id
 * @property string $externalId
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
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 *
 * @property-read User $user
 *
 * @property-read Provider $provider
 * @property-read WorkerSshKey $workerSshKey
 * @property-read Collection $logs
 * @property-read Collection $userSshKeys
 * @property-read Collection $databases
 * @property-read Collection $databaseUsers
 * @property-read Collection $pythons
 * @property-read Collection $deploySshKeys
 * @property-read ServerSshKey $serverSshKey
 * @property-read Collection $firewallRules
 *
 * @method static ServerFactory factory(...$parameters)
 */
class Server extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'type',
        'external_id',
        'public_ip',
        'ssh_port',
        'ssh_host_key',
        'sudo_password',
        'suitable',
        'available',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast. */
    protected $casts = [
        'suitable' => 'boolean',
        'available' => 'boolean',
        'sudo_password' => 'encrypted',
        'database_root_password' => 'encrypted',
    ];

    /**
     * Get SSH port attribute or the default one if it's null.
     */
    public function getSshPortAttribute(): string
    {
        return $this->attributes['ssh_port'] ?? (string) config('servers.default_ssh_port');
    }

    /**
     * Get the owner of this server.
     */
    public function getUserAttribute(): User
    {
        return $this->provider->owner;
    }

    /**
     * Open an SSH session to the server with SFTP enabled.
     */
    public function sftp(string $user = null): SFTP
    {
        /** @var SFTP $sftp */
        $sftp = $this->ssh($user, $this->newSftpSession());
        return $sftp;
    }

    /**
     * Open an SSH session to the server.
     */
    public function ssh(string $user = null, SSH2 $ssh = null): SSH2
    {
        $ssh ??= $this->newSshSession();
        $user ??= 'root';

        if (! isset($this->sshHostKey))
            $this->updateSshHostKey($ssh);

        // TODO: IMPORTANT! Figure out what to do if the host key verification fails. Read about why it may happen in normal operation. Probably will have to notify the user and then ask them to confirm that everything is OK.
        if ($ssh->getServerPublicHostKey() != $this->sshHostKey)
            throw new \RuntimeException('Host key verification failed.');

        if (! $ssh->login($user, $this->workerSshKey->privateKey))
            throw new SshAuthFailedException('Key authentication failed.');

        $ssh->setKeepAlive(1);

        return $ssh;
    }

    /**
     * Load and save the server's SSH host key.
     */
    protected function updateSshHostKey(SSH2 $ssh = null): void
    {
        $ssh ??= $this->newSshSession();

        $hostKey = $ssh->getServerPublicHostKey();

        // SSH2::getServerPublicHostKey() may return false if the key received wasn't signed correctly.
        if ($hostKey === false)
            throw new \RuntimeException('Server\'s SSH host key wasn\'t received correctly.');

        $this->update(['ssh_host_key' => $hostKey]);
    }

    /**
     * Create a new instance of an SSH session to this server.
     */
    protected function newSshSession(): SSH2
    {
        return new SSH2($this->publicIp, $this->sshPort);
    }

    /**
     * Create a new instance of an SFTP session to this server.
     */
    protected function newSftpSession(): SFTP
    {
        return new SFTP($this->publicIp, $this->sshPort);
    }

    /**
     * Create a ServerLog entry for this server.
     */
    public function log(
        string $type,
        string $command = null,
        int $exitCode = null,
        string $content = null,
        string $localFile = null,
        string $remoteFile = null,
        bool $success = null,
        CarbonInterface $timestamp = null,
    ): void {
        $this->logs()->create([
            'type' => $type,
            'command' => $command,
            'exit_code' => $exitCode,
            'content' => $content,
            'local_file' => $localFile,
            'remote_file' => $remoteFile,
            'success' => $success,
            'created_at' => $timestamp ?? now(),
        ]);
    }

    /**
     * Get a relation to the provider that runs this server.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get a relation to the SSH key used to access this server.
     */
    public function workerSshKey(): HasOne
    {
        return $this->hasOne(WorkerSshKey::class);
    }

    /**
     * Get a relation to the SSH keys added by the user for this server.
     */
    public function userSshKeys(): BelongsToMany
    {
        return $this->belongsToMany(UserSshKey::class, 'server_user_ssh_key')
            ->using(ServerUserSshKeyPivot::class)
            ->withTimestamps();
    }

    /**
     * Get a relation to the logs of operations performed on this server.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ServerLog::class);
    }

    /**
     * Get a relation with the databases this server holds.
     */
    public function databases(): HasMany
    {
        return $this->hasMany(Database::class);
    }

    /**
     * Get a relation with the database users this server has.
     */
    public function databaseUsers(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }

    /**
     * Get a relation with Python instances installed on this server.
     */
    public function pythons(): HasMany
    {
        return $this->hasMany(Python::class);
    }

    /**
     * Get a relation with the deploy SSH keys used by this server.
     */
    public function deploySshKey(): HasMany
    {
        return $this->hasMany(DeploySshKey::class);
    }

    /**
     * Get a relation with the SHH key that this server is using to access VCS repositories.
     */
    public function serverSshKey(): HasOne
    {
        return $this->hasOne(ServerSshKey::class);
    }

    /**
     * Get a relation with the firewall rules created for this server.
     */
    public function firewallRules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }
}
