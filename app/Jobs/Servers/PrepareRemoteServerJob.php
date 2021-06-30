<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\EnableFirewallScript;
use App\Scripts\Root\InitializeFirewallScript;
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
        StoreFirewallRuleAction $storeFirewallRule,
        UpgradePackagesScript $upgradePackages,
        InstallBasePackagesScript $installBasePackages,
        ConfigureUnattendedUpgradesScript $configureUnattendedUpgrades,
        InitializeFirewallScript $initializeFirewall,
        EnableFirewallScript $enableFirewall,
        CreateSudoUserScript $createSudoUser,
        ConfigureSshServerScript $configureSshServer,
        RebootServerScript $rebootServer,
    ): void {
        DB::transaction(function () use (
            $upgradePackages,
            $installBasePackages,
            $configureUnattendedUpgrades,
            $initializeFirewall,
            $enableFirewall,
            $createSudoUser,
            $configureSshServer,
            $rebootServer,
            $storeFirewallRule,
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

            $initializeFirewall->execute($server, $ssh);

            $storeFirewallRule->execute(new FirewallRuleData(
                name: 'SSH',
                port: '22',
            ), $server, true);

            $enableFirewall->execute($server, $ssh);

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
