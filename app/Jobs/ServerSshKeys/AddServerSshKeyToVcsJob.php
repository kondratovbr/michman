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
        parent::__construct();

        $this->server = $server->withoutRelations();
        $this->vcsProvider = $vcsProvider->withoutRelations();
    }

    public function handle(): void
    {
        $api = $this->vcsProvider->api();

        DB::transaction(function () use ($api) {
            $server = $this->server->freshSharedLock();
            $vcsProvider = $this->vcsProvider->freshSharedLock();

            /** @var ServerSshKey $serverSshKey */
            $serverSshKey = $server->serverSshKey()->lockForUpdate()->firstOrFail();

            $api->addSshKeySafely(
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
