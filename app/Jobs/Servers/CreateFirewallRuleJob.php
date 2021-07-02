<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class CreateFirewallRuleJob extends AbstractJob
{
    use IsInternal;

    protected Server $server;
    protected string $name;
    protected string $port;

    public function __construct(Server $server, string $name, string $port)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->name = $name;
        $this->port = $port;
    }

    /**
     * Execute the job.
     */
    public function handle(StoreFirewallRuleAction $storeFirewallRule): void
    {
        DB::transaction(function () use ($storeFirewallRule) {
            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($this->server->getKey());

            $storeFirewallRule->execute(new FirewallRuleData(
                name: $this->name,
                port: $this->port,
            ), $server);
        }, 5);
    }
}
