<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\ServerChannel;
use App\Events\Servers\ServerDeletedEvent;
use App\Events\Servers\ServerUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Server;
use Illuminate\Contracts\View\View;

// TODO: IMPORTANT! Cover with some feature tests.

/*
 * TODO: IMPORTANT! Here and elsewhere: when a model that is stored as an attribute in a Livewire component gets deleted
 *       the next refresh causes a 404 modal to appear.
 *       Should maybe figure out something more graceful.
 */

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
        // We don't want to refresh the page if a normal sub-page was shown anyway.
        if ($this->show != 'placeholder')
            return;

        $this->server->refresh();

        if ($this->server->isReady())
            $this->show = 'projects';
    }

    public function getDisabledProperty(): bool
    {
        return ! $this->server->isReady();
    }

    protected function getDefaultRoute(): string
    {
        return route('servers.show', [$this->server, static::DEFAULT_SHOW]);
    }

    public function render(): View
    {
        if (! $this->server->isReady())
            $this->show = 'placeholder';

        return parent::render();
    }
}
