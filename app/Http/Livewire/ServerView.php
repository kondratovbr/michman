<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;

class ServerView extends AbstractSubpagesView
{
    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'servers.show';

    public const VIEWS = [
        'projects' => 'projects.index',
        'pythons' => 'pythons.index',
        'firewall' => 'firewall.index',
        'database' => 'database.show',
        //
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'projects';

    public Server $server;
}
