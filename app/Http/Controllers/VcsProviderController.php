<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\Users\FlashMessageEvent;
use App\Facades\Auth;
use App\Handlers\VcsProviderHandler;
use App\Models\VcsProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use RuntimeException;

class VcsProviderController extends AbstractController
{
    public function __construct(
        private VcsProviderHandler $handler,
    ) {}

    /** Get a redirect to link a third-party VCS provider account to the current user's account. */
    public function redirect(string $vcsProviderOauthName): SymfonyRedirect
    {
        $socialite = Socialite::driver($vcsProviderOauthName);

        $vcsProviderName = $this->handler->getVcsProviderName($vcsProviderOauthName);

        if (is_null($vcsProviderName))
            throw new RuntimeException('Invalid OAuth provider - corresponding VCS provider not found.');

        return $socialite
            ->scopes(config("vcs.list.{$vcsProviderName}.oauth_scopes"))
            ->redirectUrl(route('vcs.link-callback', $vcsProviderOauthName))
            ->redirect();
    }

    /** Handle an OAuth callback from a third-party VCS provider. */
    public function link(string $vcsProviderOauthName): RedirectResponse
    {
        DB::transaction(function () use ($vcsProviderOauthName) {
            $user = Auth::user();

            $oauthUser = Socialite::driver($vcsProviderOauthName)
                ->redirectUrl(route('vcs.link-callback', $vcsProviderOauthName))
                ->user();

            $vcs = $user->vcs($this->handler->getVcsProviderName($vcsProviderOauthName));

            if (is_null($vcs)) {
                $vcs = $this->handler->createViaOAuth($vcsProviderOauthName, $oauthUser, $user);

                flash(__('flash.vcs-provider-linked', [
                    'vcs' => __("projects.repo.providers.{$vcs->provider}"),
                ]), FlashMessageEvent::STYLE_SUCCESS);
            } else {
                $this->handler->updateViaOAuth($vcsProviderOauthName, $oauthUser, $user);

                flash(__('flash.vcs-provider-updated', [
                    'vcs' => __("projects.repo.providers.{$vcs->provider}"),
                ]), FlashMessageEvent::STYLE_SUCCESS);
            }
        }, 5);

        return redirect()->route('account.show', 'vcs');
    }

    /** Disconnect the user from a third-part VCS provider account. */
    public function unlink(string $vcsProviderOauthName): RedirectResponse
    {
        $vcsProviderName = $this->handler->getVcsProviderName($vcsProviderOauthName);

        DB::transaction(function () use ($vcsProviderName) {
            /** @var VcsProvider $vcs */
            $vcs = Auth::user()->vcsProviders()
                ->where('provider', $vcsProviderName)
                ->latest()
                ->lockForUpdate()
                ->firstOrFail();

            if ($vcs->projects()->count() > 0)
                return;

            $this->authorize('delete', $vcs);

            $vcs->delete();
        }, 5);

        return redirect()->route('account.show', 'vcs');
    }
}
