<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Server;
use App\Models\ServerSshKey;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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
            $vcsProvider = $this->vcsProvider->freshLockForUpdate();

            /** @var ServerSshKey $serverSshKey */
            $serverSshKey = $vcsProvider->serverSshKeys()
                ->lockForUpdate()
                ->findOrFail($server->serverSshKey->getKey());

            if (is_null($serverSshKey->vcsProviderKey)) {
                $this->fail(new RuntimeException('vcsProviderKey pivot model isn\'t set on ServerSshKey model.'));
                return;
            }

            if (empty($serverSshKey->vcsProviderKey->externalId)) {
                $this->fail(new RuntimeException('externalId isn\'t set.'));
                return;
            }

            $api->deleteSshKey($serverSshKey->vcsProviderKey->externalId);

            $serverSshKey->vcsProviders()->detach($vcsProvider);
        }, 5);
    }
}
