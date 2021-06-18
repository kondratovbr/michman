<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\VcsProviderData;
use App\Exceptions\NotImplementedException;
use App\Facades\Auth;
use App\Models\VcsProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use RuntimeException;

class VcsProviderController extends AbstractController
{
    public function __construct(
        private StoreVcsProviderAction $storeVcsProvider,
        private UpdateVcsProviderAction $updateVcsProvider,
    ) {}

    /**
     * Get a redirect to link a third-party VCS provider account to the current user's account as a VCS provider.
     */
    public function redirect(string $vcsProviderName): SymfonyRedirect
    {
        return match ($vcsProviderName) {
            'github' => $this->redirectGithub(),
            'gitlab' => $this->redirectGitlab(),
            'bitbucket' => $this->redirectBitbucket(),
            default => throw new RuntimeException('Unknown VCS provider name passed to redirect to an OAuth page.')
        };
    }

    /**
     * Handle an OAuth callback from a third-party VCS provider.
     */
    public function callback(string $vcsProviderName): RedirectResponse
    {
        // TODO: CRITICAl! Show a success message when the link or refresh is successful.

        DB::transaction(function () use ($vcsProviderName) {
            $oauthUser = Socialite::driver($vcsProviderName)->user();

            $vcsProvider = Auth::user()->vcs($vcsProviderName, true);

            // If the user is already linked we'll just update the credentials.
            if (! is_null($vcsProvider)) {
                $this->authorize('update', $vcsProvider);

                // User cannot refresh credentials using a different account - they should unlink it first.
                // TODO: CRITICAL! Show some error to the user in such, don't just leave it like that.
                if ($vcsProvider->externalId != $oauthUser->getId())
                    return;

                $this->updateVcsProvider->execute(
                    $vcsProvider,
                    VcsProviderData::fromOauth(
                        $oauthUser,
                        $vcsProviderName,
                        Auth::user(),
                    )
                );

                return;
            }

            $this->authorize('create', [VcsProvider::class, $vcsProviderName]);

            $this->storeVcsProvider->execute(VcsProviderData::fromOauth(
                $oauthUser,
                $vcsProviderName,
                Auth::user()),
            );
        }, 5);

        return redirect()->route('account.show', 'vcs');
    }

    /**
     * Disconnect the user from a third-part VCS provider account.
     */
    public function unlink(string $vcsProviderName): RedirectResponse
    {
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
     * Get a redirect to GitHub OAuth page to link it as a VCS provider.
     */
    private function redirectGithub(): SymfonyRedirect
    {
        return Socialite::driver('github')
            ->scopes(['repo', 'admin:public_key'])
            ->with(['redirect_uri' => route('vcs.callback', 'github')])
            ->redirect();
    }

    /**
     * Get a redirect to GitLab OAuth page to link it as a VCS provider.
     */
    private function redirectGitlab(): SymfonyRedirect
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        return Socialite::driver('gitlab')
            ->scopes([])
            ->with(['redirect_uri' => route('vcs.callback', 'gitlab')])
            ->redirect();
    }

    /**
     * Get a redirect to Bitbucket OAuth page to link it as a VCS provider.
     */
    private function redirectBitbucket(): SymfonyRedirect
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        return Socialite::driver('bitbucket')
            ->scopes([])
            ->with(['redirect_uri' => route('vcs.callback', 'bitbucket')])
            ->redirect();
    }
}
