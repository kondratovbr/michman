<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Facades\Auth;
use Illuminate\Contracts\View\View;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;

class ChangeEmailForm extends Component
{
    /** @var string Currently typed email address. */
    public string $email;

    /**
     * Prepare the component.
     */
    public function mount(): void
    {
        $this->email = Auth::user()->email;
    }

    /**
     * Save the new user's email address.
     */
    public function changeEmail(UpdatesUserProfileInformation $updater): void
    {
        $this->resetErrorBag();

        // We're using a Laravel Fortify built-in updater class for this.
        $updater->update(Auth::user(), [
            'email' => $this->email,
        ]);

        // This event is used to show the success message.
        $this->emit('saved');
        // This event will refresh the navbar - to render the new email.
        $this->emit('refresh-navbar');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.change-email-form');
    }
}
