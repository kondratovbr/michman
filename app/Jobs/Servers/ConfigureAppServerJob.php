<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Databases\StoreDatabaseAction;
use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\DatabaseData;
use App\DataTransferObjects\FirewallRuleData;
use App\DataTransferObjects\NewServerData;
use App\Jobs\AbstractJob;
use App\Jobs\Databases\CreateDatabaseJob;
use App\Jobs\Databases\InstallDatabaseJob;
use App\Jobs\FirewallRules\CreateFirewallRuleJob;
use App\Jobs\Pythons\CreatePythonJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class ConfigureAppServerJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected NewServerData $data;

    public function __construct(Server $server, NewServerData $data)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(
        StoreFirewallRuleAction $storeFirewallRule,
        StoreDatabaseAction $storeDatabase,
    ): void {
        DB::transaction(function () use (
            $storeFirewallRule,
            $storeDatabase,
        ) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            Bus::chain([
                new InstallDatabaseJob($server, $this->data->database),
                new InstallCacheJob($server, $this->data->cache),
                new CreatePythonJob($server, $this->data->pythonVersion),
                new InstallNginxJob($server),
                new CreateFirewallRuleJob($server, 'HTTP', '80'),
                new CreateFirewallRuleJob($server, 'HTTPS', '443'),

                // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

                //

            ])->dispatch();

        }, 5);
    }
}
