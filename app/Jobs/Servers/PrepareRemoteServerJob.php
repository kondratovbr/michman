<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleDto;
use App\Jobs\AbstractRemoteServerJob;
use App\Notifications\Servers\FailedToPrepareServerNotification;
use App\Scripts\Root\EnableFirewallScript;
use App\Scripts\Root\InitializeFirewallScript;
use App\Scripts\Root\ConfigureSshServerScript;
use App\Scripts\Root\ConfigureUnattendedUpgradesScript;
use App\Scripts\Root\CreateSudoUserScript;
use App\Scripts\Root\InstallBasePackagesScript;
use App\Scripts\Root\InstallSnapAppsScript;
use App\Scripts\Root\RebootServerScript;
use App\Scripts\Root\UpdateSnapScript;
use App\Scripts\Root\UpgradePackagesScript;
use App\States\Servers\Failed;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! Cover with tests!

class PrepareRemoteServerJob extends AbstractRemoteServerJob
{
    public function handle(
        StoreFirewallRuleAction $storeFirewallRule,
        UpgradePackagesScript $upgradePackages,
        UpdateSnapScript $updateSnap,
        InstallBasePackagesScript $installBasePackages,
        InstallSnapAppsScript $installSnapApps,
        ConfigureUnattendedUpgradesScript $configureUnattendedUpgrades,
        InitializeFirewallScript $initializeFirewall,
        EnableFirewallScript $enableFirewall,
        CreateSudoUserScript $createSudoUser,
        ConfigureSshServerScript $configureSshServer,
        RebootServerScript $rebootServer,
    ): void {
        DB::transaction(function () use (
            $upgradePackages, $updateSnap, $installBasePackages, $installSnapApps, $configureUnattendedUpgrades,
            $initializeFirewall, $enableFirewall, $createSudoUser, $configureSshServer, $rebootServer,
            $storeFirewallRule,
        ) {
            $server = $this->server->freshLockForUpdate();

            $ssh = $server->sftp('root');

            $upgradePackages->execute($server, $ssh);

            $updateSnap->execute($server, $ssh);

            $installBasePackages->execute($server, $ssh);

            $installSnapApps->execute($server, $ssh);

            $configureUnattendedUpgrades->execute($server, $ssh);

            $initializeFirewall->execute($server, $ssh);

            $storeFirewallRule->execute(new FirewallRuleDto(
                name: 'SSH',
                port: '22',
                can_delete: false,
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
        });
    }

    public function failed(): void
    {
        $this->server->state->transitionTo(Failed::class);
        $this->server->user->notify(new FailedToPrepareServerNotification($this->server));
    }
}
