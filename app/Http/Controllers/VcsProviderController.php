<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\DataTransferObjects\VcsProviderData;
use App\Exceptions\NotImplementedException;
use App\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use RuntimeException;

class VcsProviderController extends AbstractController
{
    public function __construct(
        private StoreVcsProviderAction $createVcsProvider,
    ) {}

    /**
     * Get a redirect to link a third-party VCS provider account to the current user's account as a VCS provider.
     */
    public function redirect(string $vcsProvider): SymfonyRedirect
    {
        return match ($vcsProvider) {
            'github' => $this->redirectGithub(),
            'gitlab' => $this->redirectGitlab(),
            'bitbucket' => $this->redirectBitbucket(),
            default => throw new RuntimeException('Unknown VCS provider name passed to redirect to an OAuth page.')
        };
    }

    /**
     * Handle an OAuth callback from a third-party VCS provider.
     */
    public function callback(string $vcsProvider): RedirectResponse
    {
        switch ($vcsProvider) {
            case 'github':
                $this->linkGithub();
                break;
            case 'gitlab':
                $this->linkGitlab();
                break;
            case 'bitbucket':
                $this->linkBitbucket();
                break;
            default:
                throw new RuntimeException('Unknown VCS provider name passed to link to a VCS account.');
        }

        return redirect()->route('account.show', 'vcs');
    }

    /**
     * Get a redirect to GitHub OAuth page to link it as a VCS provider.
     */
    private function redirectGithub(): SymfonyRedirect
    {
        return Socialite::driver('github')
            ->scopes(['repo', 'admin:public_key'])
            // ->with(['redirect_uri' => route('vcs.callback', 'github')])
            ->with(['redirect_uri' => 'http://foo.bar'])
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

    /**
     * Link the user's account to a GitHub account as a VcsProvider.
     */
    private function linkGithub(): void
    {
        $oauthUser = Socialite::driver('github')->user();

        $this->createVcsProvider->execute(new VcsProviderData(
            user: Auth::user(),
            provider: 'github',
            token: $oauthUser->token,
        ));
    }

    /**
     * Link the user's account to a GitLab account as a VcsProvider.
     */
    private function linkGitlab(): void
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        $oauthUser = Socialite::driver('gitlab')->user();

        //
    }

    /**
     * Link the user's account to a BitBucket account as a VcsProvider.
     */
    private function linkBitbucket(): void
    {
        // TODO: CRITICAL! Implement.

        throw new NotImplementedException;

        $oauthUser = Socialite::driver('bitbucket')->user();

        //
    }
}
