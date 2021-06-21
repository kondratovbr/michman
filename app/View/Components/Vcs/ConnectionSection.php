<?php declare(strict_types=1);

namespace App\View\Components\Vcs;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConnectionSection extends Component
{
    public string $vcsProvider;
    public string $oauthProvider;

    public function __construct(string $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
        $this->vcsProvider = (string) config("auth.oauth_providers.{$oauthProvider}.vcs_provider");
    }

    public function render(): View
    {
        return view('components.vcs.connection-section');
    }
}
