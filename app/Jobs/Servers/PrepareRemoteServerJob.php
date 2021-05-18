<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PrepareRemoteServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

    /** @var string[] Command sequence to execute. */
    protected array $script = [
        'apt-get -y update',
        'apt-get -y upgrade',
        'apt-get -y install ufw git',
    ];

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->executeScript($server);

            //

        }, 5);
    }

    protected function executeScript(Server $server): void
    {
        // TODO: CRITICAL! CONTINUE! Figure out unattended-upgrades, see official guides.

        $commands = [
            'apt-get update -y',
            'apt-get upgrade -y',
            'apt-get install -y ufw git curl gnupg gzip unattended-upgrades',
            'ufw disable',
            'ufw logging on',
            'ufw default deny routed',
            'ufw default deny incoming',
            'ufw default allow outgoing',
            "ufw limit in {$server->sshPort}/tcp",
            'ufw enable',
            '',

            //

            'reboot',
        ];

        $ssh = $server->ssh('root');

        foreach ($commands as $command) {
            $ssh->exec($command);
        }
    }
}
