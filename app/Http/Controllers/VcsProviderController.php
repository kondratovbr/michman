<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\VcsProviderDto;
use App\Events\Users\FlashMessageEvent;
use App\Exceptions\NotImplementedException;
use App\Facades\Auth;
use App\Models\VcsProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Contracts\User as OAuthUser;

class VcsProviderController extends AbstractController
{
    public function __construct(
        private StoreVcsProviderAction $storeVcsProvider,
        private UpdateVcsProviderAction $updateVcsProvider,
    ) {}

    /** Get a redirect to link a third-party VCS provider account to the current user's account as a VCS provider. */
    public function redirect(string $vcsProviderOauthName): SymfonyRedirect
    {
        $vcsProviderName = $this->getVcsProviderName($vcsProviderOauthName);

        return Socialite::driver($vcsProviderOauthName)
            ->scopes(config("vcs.list.{$vcsProviderName}.oauth_scopes"))
            ->with(['redirect_uri' => route('vcs.callback', $vcsProviderOauthName)])
            ->redirect();
    }

    /** Handle an OAuth callback from a third-party VCS provider. */
    public function callback(string $vcsProviderOauthName): RedirectResponse
    {
        DB::transaction(function () use ($vcsProviderOauthName) {
            $vcsProviderName = $this->getVcsProviderName($vcsProviderOauthName);

            $oauthUser = Socialite::driver($vcsProviderOauthName)->user();

            $vcsProvider = Auth::user()->vcs($vcsProviderName, true);

            // If the account is already linked we'll just update the credentials.
            if (! is_null($vcsProvider)) {
                $this->updateVcsProvider($vcsProvider, $oauthUser, $vcsProviderName);
                return;
            }

            $this->createVcsProvider($oauthUser, $vcsProviderName);
        }, 5);

        return redirect()->route('account.show', 'vcs');
    }

    protected function updateVcsProvider(VcsProvider $vcsProvider, OAuthUser $oauthUser, string $vcsProviderName): VcsProvider
    {
        $this->authorize('update', $vcsProvider);

        // User cannot refresh credentials using a different account - they should unlink it first.
        if ($vcsProvider->externalId != $oauthUser->getId()) {
            event(new FlashMessageEvent(
                Auth::user(),
                __('flash.vcs-provider-link-failed-different-account', [
                    'vcs' => __("projects.repo.providers.{$vcsProvider->provider}"),
                ]),
                FlashMessageEvent::STYLE_DANGER,
            ));

            return $vcsProvider;
        }

        $vcsProvider = $this->updateVcsProvider->execute(
            $vcsProvider,
            VcsProviderDto::fromOauth(
                $oauthUser,
                $vcsProviderName,
            )
        );

        event(new FlashMessageEvent(
            Auth::user(),
            __('flash.vcs-provider-updated', [
                'vcs' => __("projects.repo.providers.{$vcsProvider->provider}"),
            ]),
            FlashMessageEvent::STYLE_INFO,
        ));

        return $vcsProvider;
    }

    protected function createVcsProvider(OAuthUser $oauthUser, string $vcsProviderName): VcsProvider
    {
        $this->authorize('create', [VcsProvider::class, $vcsProviderName]);

        $vcsProvider = $this->storeVcsProvider->execute(VcsProviderDto::fromOauth(
            $oauthUser,
            $vcsProviderName
        ), Auth::user());

        event(new FlashMessageEvent(
            Auth::user(),
            __('flash.vcs-provider-linked', [
                'vcs' => __("projects.repo.providers.{$vcsProvider->provider}"),
            ]),
            FlashMessageEvent::STYLE_SUCCESS,
        ));

        return $vcsProvider;
    }

    /** Disconnect the user from a third-part VCS provider account. */
    public function unlink(string $vcsProviderOauthName): RedirectResponse
    {
        $vcsProviderName = $this->getVcsProviderName($vcsProviderOauthName);

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = Auth::user()->vcsProviders()
            ->where('provider', $vcsProviderName)
            ->latest()
            ->lockForUpdate()
            ->firstOrFail();

        $this->authorize('delete', $vcsProvider);

        // TODO: CRITICAL! Implement!

        throw new NotImplementedException;

        //
    }

    /** Get a VCS provider name from config by its OAuth provider name. */
    private function getVcsProviderName(string $vcsProviderOauthName): string
    {
        return (string) config("auth.oauth_providers.{$vcsProviderOauthName}.vcs_provider");
    }
}
