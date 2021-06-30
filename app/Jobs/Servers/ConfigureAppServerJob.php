<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\DataTransferObjects\NewServerData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class ConfigureAppServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected NewServerData $data;

    public function __construct(Server $server, NewServerData $data)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
        $this->data = $data;
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
                new InstallDatabaseJob($server, $this->data->database),
                new CreateDatabaseJob($server, $this->data->dbName),
                new InstallCacheJob($server, $this->data->cache),
                new CreatePythonJob($server, $this->data->pythonVersion),
                new InstallNginxJob($server),

                // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

                //

            ])->dispatch();

            $storeFirewallRuleAction->execute(new FirewallRuleData(
                name: 'HTTP',
                port: '80',
            ), $server);

            $storeFirewallRuleAction->execute(new FirewallRuleData(
                name: 'HTTPS',
                port: '443',
            ), $server);

        }, 5);
    }
}
