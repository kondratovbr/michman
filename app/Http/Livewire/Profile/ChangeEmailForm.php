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
     * Initialize the component.
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
        // The validation is done by the Updater object,
        // which is currently an instance of
        // App\Actions\Fortify\UpdateUserProfileInformation

        $this->resetErrorBag();

        $updater->update(Auth::user(), [
            'email' => $this->email,
        ]);

        // This event is used to show the success message.
        $this->emit('saved');
        // This event will refresh the navigation menus - to render the new email.
        $this->emit('refresh-navigation');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.change-email-form');
    }
}
