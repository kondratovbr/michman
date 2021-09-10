<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleDto;
use App\DataTransferObjects\NewServerDto;
use App\Jobs\AbstractJob;
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
    protected NewServerDto $data;

    public function __construct(Server $server, NewServerDto $data)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(StoreFirewallRuleAction $storeFirewallRule): void {
        DB::transaction(function () use ($storeFirewallRule) {
            $server = $this->server->freshLockForUpdate();

            $storeFirewallRule->execute(new FirewallRuleDto(
                name: 'HTTP',
                port: '80',
            ), $server);
            $storeFirewallRule->execute(new FirewallRuleDto(
                name: 'HTTPS',
                port: '443',
            ), $server);

            Bus::chain([
                new InstallDatabaseJob($server, $this->data->database),
                new InstallCacheJob($server, $this->data->cache),
                new CreatePythonJob($server, $this->data->python_version),
                new InstallNginxJob($server),

                // TODO: CRITICAL! Don't forget the rest of the stuff I maybe should do here!

            ])->dispatch();

        }, 5);
    }
}
