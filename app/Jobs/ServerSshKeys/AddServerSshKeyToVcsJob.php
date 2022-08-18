<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Server;
use App\Models\ServerSshKey;
use App\Models\VcsProvider;
use App\Notifications\Providers\AddingSshKeyToProviderFailedNotification;
use Illuminate\Support\Facades\DB;

// TODO: Update tests to check that the pivot was created and external ID was stored.

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

        if (! $api->supportsSshKeys()) {
            $this->fail();
            return;
        }

        DB::transaction(function () use ($api) {
            $server = $this->server->freshSharedLock();
            $vcsProvider = $this->vcsProvider->freshSharedLock();

            /** @var ServerSshKey $serverSshKey */
            $serverSshKey = $server->serverSshKey()->lockForUpdate()->firstOrFail();

            $keyData = $api->addSshKeySafely(
                $serverSshKey->name,
                $serverSshKey->getPublicKeyString(false)
            );

            $serverSshKey->vcsProviders()->attach($vcsProvider, ['external_id' => $keyData->id]);
        });
    }

    public function failed(): void
    {
        $this->server->provider->user->notify(
            new AddingSshKeyToProviderFailedNotification($this->server->provider)
        );
    }
}
