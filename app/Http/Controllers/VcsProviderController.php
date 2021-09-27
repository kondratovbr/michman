<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\VcsProviderDto;
use App\Exceptions\NotImplementedException;
use App\Facades\Auth;
use App\Models\VcsProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

// TODO: CRITICAL! Cover with tests!

class VcsProviderController extends AbstractController
{
    public function __construct(
        private StoreVcsProviderAction $storeVcsProvider,
        private UpdateVcsProviderAction $updateVcsProvider,
    ) {}

    /**
     * Get a redirect to link a third-party VCS provider account to the current user's account as a VCS provider.
     */
    public function redirect(string $vcsProviderOauthName): SymfonyRedirect
    {
        $vcsProviderName = $this->getVcsProviderName($vcsProviderOauthName);

        return Socialite::driver($vcsProviderOauthName)
            ->scopes(config("vcs.list.{$vcsProviderName}.oauth_scopes"))
            ->with(['redirect_uri' => route('vcs.callback', $vcsProviderOauthName)])
            ->redirect();
    }

    /**
     * Handle an OAuth callback from a third-party VCS provider.
     */
    public function callback(string $vcsProviderOauthName): RedirectResponse
    {
        // TODO: CRITICAl! Show a success message when the link or refresh is successful.

        DB::transaction(function () use ($vcsProviderOauthName) {
            $vcsProviderName = $this->getVcsProviderName($vcsProviderOauthName);

            $oauthUser = Socialite::driver($vcsProviderOauthName)->user();

            $vcsProvider = Auth::user()->vcs($vcsProviderName, true);

            // If the user is already linked we'll just update the credentials.
            if (! is_null($vcsProvider)) {
                $this->authorize('update', $vcsProvider);

                // User cannot refresh credentials using a different account - they should unlink it first.
                // TODO: CRITICAL! Show some error to the user in such case, don't just leave it like that.
                if ($vcsProvider->externalId != $oauthUser->getId())
                    return;

                $this->updateVcsProvider->execute(
                    $vcsProvider,
                    VcsProviderDto::fromOauth(
                        $oauthUser,
                        $vcsProviderName,
                        Auth::user(),
                    )
                );

                return;
            }

            $this->authorize('create', [VcsProvider::class, $vcsProviderName]);

            $this->storeVcsProvider->execute(VcsProviderDto::fromOauth(
                $oauthUser,
                $vcsProviderName
            ), Auth::user());
        }, 5);

        return redirect()->route('account.show', 'vcs');
    }

    /**
     * Disconnect the user from a third-part VCS provider account.
     */
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

    /**
     * Get a VCS provider name from config by its OAuth provider name.
     */
    private function getVcsProviderName(string $vcsProviderOauthName): string
    {
        return (string) config("auth.oauth_providers.{$vcsProviderOauthName}.vcs_provider");
    }
}
