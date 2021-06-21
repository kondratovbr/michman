<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Server;
use App\Models\ServerSshKey;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;

class AddServerSshKeyToVcsJob extends AbstractJob
{
    use InteractsWithVcsProviders;

    protected Server $server;
    protected VcsProvider $vcsProvider;

    public function __construct(Server $server, VcsProvider $vcsProvider)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
        $this->vcsProvider = $vcsProvider->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($this->server->getKey());

            /** @var ServerSshKey $serverSshKey */
            $serverSshKey = $server->serverSshKey()->lockForUpdate()->firstOrFail();

            /** @var VcsProvider $vcsProvider */
            $vcsProvider = VcsProvider::query()
                ->lockForUpdate()
                ->findOrFail($this->vcsProvider->getKey());

            $vcsProvider->api()->addSshKeySafely(
                $serverSshKey->name,
                $serverSshKey->getPublicKeyString(false)
            );
        }, 5);
    }
}
