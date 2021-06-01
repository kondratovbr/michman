<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Servers\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ConfigureAppServerJob extends AbstractJob
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
    public function handle(StoreFirewallRuleAction $storeFirewallRuleAction): void {
        DB::transaction(function () use ($storeFirewallRuleAction) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            Bus::chain([
                new InstallNginxJob($server),
                new InstallGunicornJob($server),

                // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

                //

            ])->dispatch();

            $storeFirewallRuleAction->execute(new FirewallRuleData(
                server: $server,
                name: 'HTTP',
                port: '80',
            ));

            $storeFirewallRuleAction->execute(new FirewallRuleData(
                server: $server,
                name: 'HTTPS',
                port: '443',
            ));

        }, 5);
    }
}
