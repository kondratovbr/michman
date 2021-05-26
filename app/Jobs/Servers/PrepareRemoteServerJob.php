<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\ConfigureFirewallScript;
use App\Scripts\Root\ConfigureSshServerScript;
use App\Scripts\Root\ConfigureUnattendedUpgradesScript;
use App\Scripts\Root\CreateSudoUserScript;
use App\Scripts\Root\DisableSshAccessForUserScript;
use App\Scripts\Root\InstallBasePackagesScript;
use App\Scripts\Root\RebootServerScript;
use App\Scripts\Root\UpgradePackagesScript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        InstallBasePackagesScript $installBasePackages,
        ConfigureUnattendedUpgradesScript $configureUnattendedUpgrades,
        ConfigureFirewallScript $configureFirewall,
        CreateSudoUserScript $createSudoUser,
        AddSshKeyToUserScript $addSshKeyToUser,
        ConfigureSshServerScript $configureSshServer,
        RebootServerScript $rebootServer,
    ): void {
        DB::transaction(function () use (
            $upgradePackages,
            $installBasePackages,
            $configureUnattendedUpgrades,
            $configureFirewall,
            $createSudoUser,
            $addSshKeyToUser,
            $configureSshServer,
            $rebootServer,
        ) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $ssh = $server->sftp('root');

            $upgradePackages->execute($server, $ssh);

            $installBasePackages->execute($server, $ssh);

            $configureUnattendedUpgrades->execute($server, $ssh);

            $configureFirewall->execute($server, $ssh);

            $createSudoUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->sudoPassword,
                $ssh,
            );

            $addSshKeyToUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->workerSshKey,
                $ssh,
            );

            $configureSshServer->execute($server, $ssh);

            $rebootServer->execute($server, $ssh);

        }, 5);
    }
}
