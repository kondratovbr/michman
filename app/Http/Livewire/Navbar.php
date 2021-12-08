<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\UserChannel;
use App\Events\Projects\ProjectCreatedEvent;
use App\Events\Projects\ProjectDeletedEvent;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Events\Servers\ServerCreatedEvent;
use App\Events\Servers\ServerDeletedEvent;
use App\Events\Servers\ServerUpdatedEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Navbar extends Component
{
    use ListensForEchoes;

    /** @var string[] */
    protected $listeners = [
        // TODO: Do I use all of them? Probably only the first one.
        'refresh-navigation' => '$refresh',
        'refresh-navbar' => '$refresh',
        'refresh-navigation-menu' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(Auth::user()),
            [
                ServerCreatedEvent::class,
                ServerUpdatedEvent::class,
                ServerDeletedEvent::class,
                ProjectCreatedEvent::class,
                ProjectUpdatedEvent::class,
                ProjectDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function render(): View
    {
        $user = Auth::user()->load('servers');

        return view('livewire.navbar', [
            'user' => $user,
        ]);
    }
}
