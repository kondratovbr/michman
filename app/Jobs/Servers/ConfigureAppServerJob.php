<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleDto;
use App\DataTransferObjects\NewServerDto;
use App\Jobs\AbstractJob;
use App\Jobs\Pythons\CreatePythonJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use App\Notifications\Servers\FailedToConfigureServerNotification;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! Cover with tests!

class ConfigureAppServerJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected NewServerDto $data;

    public function __construct(Server $server, NewServerDto $data)
    {
        parent::__construct();

        $this->server = $server->withoutRelations();
        $this->data = $data;
    }

    public function handle(StoreFirewallRuleAction $storeFirewallRule): void
    {
        DB::transaction(function () use ($storeFirewallRule) {
            $server = $this->server->freshLockForUpdate();

            /*
             * TODO: IMPORTANT! Right now Python installation job is running outside of this chain,
             *       so if it fails the server is still marked as ready.
             *       Same story with firewall rules.
             *       Should fix this somehow.
             * TODO: The actual way to solve this problem is to have my own job dispatching logic that can
             *       chain all the jobs created downstream in one chain.
             *
             * Rethinking actions is an option, but then nested actions are still a massive hassle.
             *
             * There is ->prependToChain() and ->appendToChain() on jobs.
             */

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
                new InstallPlaceholderPageJob($server),
                new MarkServerAsReadyJob($server),
            ])->dispatch();

        }, 5);
    }

    public function failed(): void
    {
        $this->server->user->notify(new FailedToConfigureServerNotification($this->server));
    }
}
