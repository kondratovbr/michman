<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\ServerChannel;
use App\Events\Servers\ServerDeletedEvent;
use App\Events\Servers\ServerUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Server;
use Illuminate\Contracts\View\View;

// TODO: CRITICAL! Cover with some feature tests.

class ServerView extends AbstractSubpagesView
{
    use ListensForEchoes;

    protected const LAYOUT = 'layouts.app-with-menu';

    protected const VIEW = 'servers.show';

    public const VIEWS = [
        'projects' => 'projects.index',
        'databases' => 'databases.index',
        'pythons' => 'pythons.index',
        'firewall' => 'firewall.index',
        'ssl' => 'servers.ssl',
        'daemons' => 'daemons.index',
        //

        // This special sub-page will be shown when the server isn't ready to not confuse users.
        'placeholder' => 'servers.placeholder',
    ];

    /** @var string The name of a sub-page that will be shown by default. */
    protected const DEFAULT_SHOW = 'projects';

    public Server $server;

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            ServerUpdatedEvent::class,
            'serverUpdated',
        );
        $this->echoPrivate(
            ServerChannel::name($this->server),
            ServerDeletedEvent::class,
            'serverDeleted',
        );
    }

    /** Reload the server model from the database. */
    public function serverUpdated(): void
    {
        // We don't want to refresh the page if the server was already ready.
        if ($this->server->isReady())
            return;

        $this->server->refresh();

        if ($this->server->isReady())
            $this->show = 'projects';
    }

    // TODO: CRITICAL! Try this out. Just out of curiosity. Will Livewire try to retrieve the deleted model from the DB?
    /** If the server got deleted somehow - redirect user to the servers index page. */
    public function serverDeleted(): void
    {
        $this->redirectRoute('servers.index');
    }

    public function getDisabledProperty(): bool
    {
        return ! $this->server->isReady();
    }

    public function render(): View
    {
        if (! $this->server->isReady())
            $this->show = 'placeholder';

        return parent::render();
    }
}
