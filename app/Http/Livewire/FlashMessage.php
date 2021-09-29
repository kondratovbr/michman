<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\UserChannel;
use App\Events\Users\FlashMessageEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

class FlashMessage extends LivewireComponent
{
    use ListensForEchoes;

    public bool $show = false;
    public string|null $message = null;
    public string|null $style = null;

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(Auth::user()),
            [
                FlashMessageEvent::class,
            ],
            'flash',
        );
    }

    public function flash(array $data): void
    {
        // TODO: CRITICAL! Figure out how to fade out the currently shown message if there's one and fade in the new one.
        // TODO: CRITICAL! Also figure out how to quickly throw a flash message front-to-front without this component being involved at all - works quicker. Using Alpine events, obviously.

        $this->show = true;
        $this->message = $data['message'] ?? null;
        $this->style = $data['style'] ?? null;
    }

    public function updatedShow(): void
    {
        ray('updatedShow');

        $this->reset();
    }

    public function render(): View
    {
        return view('livewire.flash-message');
    }
}
