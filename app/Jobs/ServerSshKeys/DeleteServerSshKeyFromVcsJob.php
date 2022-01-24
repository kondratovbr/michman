<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Server;
use App\Models\ServerSshKey;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;

class DeleteServerSshKeyFromVcsJob extends AbstractJob
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

        if (! $api->supportsSshKeys())
            return;

        DB::transaction(function () use ($api) {
            $server = $this->server->freshSharedLock();
            $vcsProvider = $this->vcsProvider->freshSharedLock();

            /** @var ServerSshKey $serverSshKey */
            $serverSshKey = $server->serverSshKey()->lockForUpdate()->firstOrFail();

            // TODO: CRITICAL! CONTINUE. To do this I first have to establish a relation between ServerSshKey and VcsProvider and store the external ID on the pivot model when received. (At the moment of adding it, in the AddServerSshKeyToVcsJob.)

            // $api->deleteSshKey($serverSshKey->);
        }, 5);
    }
}
