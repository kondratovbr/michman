<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallCacheJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string $cache;

    public function __construct(Server $server, string $cache)
    {
        $this->setQueue('default');

        $this->server = $server->withoutRelations();
        $this->cache = $cache;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (is_null($this->cache) || $this->cache === 'none')
            return;

        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! Arr::hasValue(config("servers.types.{$server->type}.install"), 'cache')) {
                $this->fail(new RuntimeException('This type of server should not have a cache installed.'));
                return;
            }

            if (! is_null($server->installedCache)) {
                $this->fail(new RuntimeException('Server already has a cache installed.'));
                return;
            }

            $scriptClass = (string) config("servers.caches.{$this->cache}.scripts_namespace") . '\InstallCacheScript';

            if (! class_exists($scriptClass))
                throw new RuntimeException('No installation script exists for this cache.');

            $script = App::make($scriptClass);

            $script->execute($server);

            $server->installedCache = $this->cache;

            $server->save();
        }, 5);
    }
}
