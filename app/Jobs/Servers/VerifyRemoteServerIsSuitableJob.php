<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Models\Server;
use App\Support\Str;
use Composer\Semver\Comparator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SSH2;

class VerifyRemoteServerIsSuitableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

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

            try {
                $ssh = $server->ssh('root');
            } catch (SshAuthFailedException $e) {
                $server->suitable = false;
                $server->save();
                return;
            }

            $server->suitable = $this->serverSuitable($ssh);
            $server->save();
        }, 5);
    }

    /**
     * Determine if the server is suitable for use with the app.
     */
    protected function serverSuitable(SSH2 $ssh): bool
    {
        // TODO: IMPORTANT! Must test this whole thing with other providers. Only tested on DigitalOcean so far. Add some random generic VPSs as well.

        // Server is running Ubuntu.
        if (! Str::contains($ssh->exec('uname -v'), ['ubuntu', 'Ubuntu']))
            return false;

        // The version of Ubuntu is relatively recent - at least 16.04.
        $version = $ssh->exec('lsb_release -sr');
        if ($ssh->getExitStatus() || Comparator::lessThan($version, '16.04'))
            return false;

        // We have root access at the moment.
        if ($ssh->exec('whoami') != 'root')
            return false;

        // apt-get is installed and accessible.
        $ssh->exec('apt-get -v');
        if ($ssh->getExitStatus())
            return false;

        return true;
    }
}
