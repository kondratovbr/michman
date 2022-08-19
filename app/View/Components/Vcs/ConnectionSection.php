<?php declare(strict_types=1);

namespace App\View\Components\Vcs;

use App\Facades\Auth;
use App\Models\VcsProvider;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConnectionSection extends Component
{
    public string $vcsProviderName;
    public string $oauthProvider;
    public VcsProvider|null $vcsProvider;

    public function __construct(string $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
        $this->vcsProviderName = (string) config("auth.oauth_providers.$oauthProvider.vcs_provider");
        $this->vcsProvider = user()->vcsProviders()
            ->where('provider', $this->vcsProviderName)
            ->with('projects')
            ->first();
    }

    /** Check if this VcsProvider can be safely unlinked. */
    public function canUnlink(): bool
    {
        return $this->vcsProvider->projects->count() == 0;
    }

    /** Check is this VcsProvider is in use by some projects. */
    public function inUse(): bool
    {
        return $this->vcsProvider->projects->count() > 0;
    }

    public function render(): View
    {
        return view('components.vcs.connection-section');
    }
}
