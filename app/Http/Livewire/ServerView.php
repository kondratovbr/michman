<?php declare(strict_types=1);

namespace App\Http\Livewire;

class ServerView extends AbstractSubpagesView
{
    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'servers.show';

    public const VIEWS = [
        'projects' => 'projects.show',
        'pythons' => 'pythons.show',
        //
    ];

    // TODO: CRITICAL! Don't forget to change this default once ready. Should be on server's projects index.
    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'pythons';
}
