<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\DataTransferObjects\VcsProviderData;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;

class UpdateVcsProviderAction
{
    public function execute(VcsProvider $vcsProvider, VcsProviderData $data): VcsProvider
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
