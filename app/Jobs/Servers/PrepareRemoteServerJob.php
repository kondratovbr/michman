<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\ConfigureFirewallScript;
use App\Scripts\Root\ConfigureSshServerScript;
use App\Scripts\Root\ConfigureUnattendedUpgradesScript;
use App\Scripts\Root\CreateSudoUserScript;
use App\Scripts\Root\DisableSshAccessForUserScript;
use App\Scripts\Root\RebootServerScript;
use App\Scripts\Root\UpgradePackagesScript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SFTP;

/*
 * TODO: IMPORTANT! I should somehow log everything the app does on the servers for possible troubleshooting.
 */

class PrepareRemoteServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 30; // 30 min

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(
        UpgradePackagesScript $upgradePackages,
        ConfigureUnattendedUpgradesScript $configureUnattendedUpgrades,
        ConfigureFirewallScript $configureFirewall,
        CreateSudoUserScript $createSudoUser,
        AddSshKeyToUserScript $addSshKeyToUser,
        ConfigureSshServerScript $configureSshServer,
        DisableSshAccessForUserScript $disableSshAccessForUser,
        RebootServerScript $rebootServer,
    ): void {
        DB::transaction(function () use (
            $upgradePackages,
            $configureUnattendedUpgrades,
            $configureFirewall,
            $createSudoUser,
            $addSshKeyToUser,
            $configureSshServer,
            $disableSshAccessForUser,
            $rebootServer,
        ) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $ssh = $server->sftp('root');

            $upgradePackages->execute($server, $ssh);

            $configureUnattendedUpgrades->execute($server, $ssh);

            $configureFirewall->execute($server, $ssh);

            $createSudoUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->sudoPassword,
                $ssh,
            );
            $server->sudoPassword = null;
            $server->save();

            $addSshKeyToUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->workerSshKey,
                $ssh,
            );

            $configureSshServer->execute($server, $ssh);

            $disableSshAccessForUser->execute($server, 'root', $ssh);

            $rebootServer->execute($server, $ssh);

        }, 5);
    }

    protected function executeScript(Server $server): void
    {
        // TODO: CRITICAL! Test the whole thing!

        $ssh = $server->sftp('root');

        // Update packages and make sure the required ones are installed.
        $ssh->enablePTY();
        $ssh->setTimeout(60 * 15); // 15 min
        // TODO: IMPORTANT! Make sure to handle a situation when an apt-get gets interrupted by something (like an outage of sorts) so
        //       'dpkg was interrupted, you must manually run 'dpkg --configure -a' to correct the problem.'
        //       message shows the next time. Notify myself on an emergency channel since this will probably require some manual fixing.
        $ssh->exec('apt-get update -y');
        $ssh->read();
        $ssh->exec('apt-get upgrade --with-new-pkgs -y');
        $ssh->read();
        $ssh->exec('apt-get install -y ufw git curl gnupg gzip unattended-upgrades');
        $ssh->read();
        $ssh->disablePTY();

        // Send unattended-upgrades config files over SFTP.
        $ssh->put(
            '/etc/apt/apt.conf.d/20auto-upgrades',
            base_path('servers/apt/20auto-upgrades'),
            SFTP::SOURCE_LOCAL_FILE
        );
        $ssh->put(
            '/etc/apt/apt.conf.d/50unattended-upgrades',
            base_path('servers/apt/50unattended-upgrades'),
            SFTP::SOURCE_LOCAL_FILE
        );

        // Configure firewall using UFW.
        $ssh->setTimeout(60 * 5); // 5 min
        foreach ([
            'ufw disable',
            'ufw logging on',
            'ufw default deny routed',
            'ufw default deny incoming',
            'ufw default allow outgoing',
            "ufw limit in {$server->sshPort}/tcp",
            'ufw --force enable',
            'ufw status verbose',
        ] as $command) {
            $ssh->exec($command);
        }

        // Create a worker user.
        $ssh->setTimeout(60 * 5); // 5 min
        $ssh->exec('useradd --create-home ' . config('servers.worker_user'));
        $ssh->exec('echo ' . config('servers.worker_user') .':' . $server->sudoPassword . ' | chpasswd');

        // Add the worker user to sudo group.
        $ssh->exec('usermod -aG sudo ' . config('servers.worker_user'));

        // Set up SSH access for the worker user.
        $remoteDirectory = $remoteFile = '/home/' . config('servers.worker_user') . '/.ssh';
        $remoteFile = $remoteDirectory . '/authorized_keys';
        $ssh->mkdir($remoteDirectory, 0700);
        $ssh->touch($remoteFile);
        $ssh->exec('echo "' . $server->workerSshKey->publicKeyString . '" >> ' . $remoteFile);
        $ssh->chmod(0600, $remoteFile);
        $ssh->exec('chown -R michman ' . $remoteDirectory);
        $ssh->exec('chgrp -R michman ' . $remoteDirectory);

        // Send SSH server config and disable SSH access for root.
        $ssh->put(
            '/etc/ssh/sshd_config',
            base_path('servers/ssh/sshd_config'),
            SFTP::SOURCE_LOCAL_FILE
        );
        $ssh->delete('/root/.ssh');

        // Reboot just in case some updates needed it.
        $ssh->exec('reboot');
    }
}
