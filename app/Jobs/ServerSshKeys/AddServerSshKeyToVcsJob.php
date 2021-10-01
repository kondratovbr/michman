<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Server;
use App\Models\ServerSshKey;
use App\Models\VcsProvider;
use App\Notifications\Providers\AddingSshKeyToProviderFailedNotification;
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

    public function handle(): void
    {
        DB::transaction(function () {
            $server = $this->server->freshSharedLock();

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

    public function failed(): void
    {
        $this->server->provider->user->notify(new AddingSshKeyToProviderFailedNotification($this->server->provider));
    }
}
