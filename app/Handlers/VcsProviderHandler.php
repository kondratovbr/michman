<?php declare(strict_types=1);

namespace App\Handlers;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\VcsProviderDto;
use App\Models\User;
use App\Models\VcsProvider;
use Laravel\Socialite\Contracts\User as OAuthUser;

class VcsProviderHandler extends AbstractHandler
{
    public function __construct(
        private StoreVcsProviderAction $storeVcsProvider,
        private UpdateVcsProviderAction $updateVcsProvider,
    ) {}

    /** Create a new VcsProvider for a user if possible. */
    public function createViaOAuth(string $oauthProvider, OAuthUser $oauthUser, User $user): VcsProvider|null
    {
        $vcsProviderName = $this->getVcsProviderName($oauthProvider);

        // This OAuth provider is not configured to be used as a VCS provider.
        if (is_null($vcsProviderName))
            return null;

        // The user already has this VCS service connected.
        if ($user->vcs($vcsProviderName))
            return null;

        $vcsProviderData = VcsProviderDto::fromOauth(
            $oauthUser,
            $vcsProviderName,
        );

        return $this->storeVcsProvider->execute($vcsProviderData, $user);
    }

    /** Update existing VcsProvider for an existing user. */
    public function updateViaOAuth(string $oauthProvider, OAuthUser $oauthUser, User $user): void
    {
        $vcsProviderName = $this->getVcsProviderName($oauthProvider);

        // This OAuth provider is not configured to be used as a VCS provider.
        if (is_null($vcsProviderName))
            return;

        $vcsProviderData = VcsProviderDto::fromOauth(
            $oauthUser,
            $vcsProviderName,
        );

        $vcsProvider = $user->vcs($vcsProviderName, true);

        if (is_null($vcsProvider))
            return;

        if ($vcsProvider->externalId != $vcsProviderData->external_id) {
            flash(__('flash.vcs-provider-link-failed-different-account', [
                'vcs' => __("projects.repo.providers.{$vcsProvider->provider}"),
            ]));
            return;
        }

        $this->updateVcsProvider->execute($vcsProvider,$vcsProviderData);
    }

    /** Get a VCS provider name from config by its OAuth provider name. */
    public function getVcsProviderName(string $oauthProviderName): string|null
    {
        return config("auth.oauth_providers.{$oauthProviderName}.vcs_provider");
    }
}
