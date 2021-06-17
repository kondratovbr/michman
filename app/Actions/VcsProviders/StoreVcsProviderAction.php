<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\DataTransferObjects\VcsProviderData;
use App\Models\User;
use App\Models\VcsProvider;

class StoreVcsProviderAction
{
    public function execute(VcsProviderData $data): VcsProvider
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = $data->user->vcsProviders()->create($data->toArray());

        return $vcsProvider;
    }
}
