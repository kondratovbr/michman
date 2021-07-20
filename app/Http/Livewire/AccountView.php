<?php declare(strict_types=1);

namespace App\Http\Livewire;

class AccountView extends AbstractSubpagesView
{
    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'account.show';

    public const VIEWS = [
        'profile' => 'profile.show',
        'providers' => 'providers.show',
        'vcs' => 'vcs.show',
        //
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'profile';
}
