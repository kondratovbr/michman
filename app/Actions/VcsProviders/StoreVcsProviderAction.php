<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\DataTransferObjects\VcsProviderDto;
use App\Models\User;
use App\Models\VcsProvider;

class StoreVcsProviderAction
{
    public function execute(VcsProviderDto $data, User $user): VcsProvider
    {
        /*
         * TODO: CRITICAL! I should somehow handle a situation when the third-party account is already linked to some other Michman user.
         *       Is it normal? Can we work like that? Maybe just warn the user?
         */
        
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = $user->vcsProviders()->create($data->toAttributes());

        return $vcsProvider;
    }
}
