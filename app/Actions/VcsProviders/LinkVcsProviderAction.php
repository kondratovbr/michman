<?php declare(strict_types=1);

namespace App\Actions\VcsProviders;

use App\Models\User;
use App\Models\VcsProvider;
use Laravel\Socialite\Contracts\User as OauthUser;

class LinkVcsProviderAction
{
    public function __construct(
        private StoreVcsProviderAction $storeVcsProvider,
    ) {}

    public function execute(OauthUser $oauthUser, string $providerName, User $user): VcsProvider
    {
        // TODO: CRITICAL! CONTINUE! Refactor VcsProviderController - move the corresponding logic here.

        //
    }
}
