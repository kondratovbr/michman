<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\DataTransferObjects\VcsProviderDto;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class UpdateVcsProviderAction
{
    public function execute(VcsProvider $vcsProvider, VcsProviderDto $data): VcsProvider
    {
        return DB::transaction(function () use ($vcsProvider, $data) {
            $vcsProvider = VcsProvider::query()
                ->lockForUpdate()
                ->findOrFail($vcsProvider->getKey());

            $vcsProvider->update($data->toArray());

            return $vcsProvider;
        }, 5);
    }
}
