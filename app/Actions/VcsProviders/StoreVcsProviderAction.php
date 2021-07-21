<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\DataTransferObjects\VcsProviderData;
use App\Models\VcsProvider;

class StoreVcsProviderAction
{
    public function execute(VcsProviderData $data): VcsProvider
    {
        /*
         * TODO: CRITICAL! I should somehow handle a situation when the third-party account is already linked to some other Michman user.
         *       Is it normal? Can we work like that? Maybe just warn the user?
         */
        
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = $data->user->vcsProviders()->create($data->toArray());

        return $vcsProvider;
    }
}
