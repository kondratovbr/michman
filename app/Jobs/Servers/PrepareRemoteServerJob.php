<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SFTP;

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
        // TODO: CRITICAL! Test the whole thing!

        // Basic operations - software updates, basic firewall configuration.
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
            'ufw --force enable',
        ];

        $ssh = $server->sftp('root');

        foreach ($commands as $command) {
            $ssh->exec($command);
        }

        // Send unattended-upgrades config files over SFTP.
        $ssh->put(
            '/etc/apt/apt.conf.d/20auto-upgrades',
            base_path('servers/apt/20auto-upgrades'),
            SFTP::SOURCE_LOCAL_FILE
        );
        $ssh->put(
            '/etc/apt/apt.conf.d/50unattended-upgrades',
            base_path('servers/apt/50unattended-upgrades'),
            SFTP::SOURCE_LOCAL_FILE
        );

        // TODO: CRITICAL! CONTINUE!
        // Creating a new user will require an interactive shell.
        $ssh->read();
        $ssh->write('');

        $ssh->exec('reboot');
    }
}
