<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\ConfigureFirewallScript;
use App\Scripts\Root\ConfigureSshServerScript;
use App\Scripts\Root\ConfigureUnattendedUpgradesScript;
use App\Scripts\Root\CreateSudoUserScript;
use App\Scripts\Root\InstallBasePackagesScript;
use App\Scripts\Root\RebootServerScript;
use App\Scripts\Root\UpgradePackagesScript;
use Illuminate\Support\Facades\DB;

/*
 * TODO: IMPORTANT! Should I cover all these server interacting jobs with tests?
 *       I don't think I can automatically test the scripts, but at least test the jobs.
 */

class PrepareRemoteServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('servers');

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
        ConfigureSshServerScript $configureSshServer,
        RebootServerScript $rebootServer,
    ): void {
        DB::transaction(function () use (
            $upgradePackages,
            $installBasePackages,
            $configureUnattendedUpgrades,
            $configureFirewall,
            $createSudoUser,
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

            $configureSshServer->execute($server, $ssh);

            $rebootServer->execute($server, $ssh);

        }, 5);
    }
}
