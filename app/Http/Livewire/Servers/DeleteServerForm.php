<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Actions\Servers\DeleteServerAction;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: Cover with tests.

/*
 * TODO: IMPORTANT! Can I refactor the confirmation modal logic into a trait?
 *       I have two types of them - the normal ones (single button) and the ones that require
 *       to type the name of a thing as a confirmation, like here.
 */

class DeleteServerForm extends LivewireComponent
{
    use AuthorizesRequests;

    public Server $server;

    public bool $confirmationModalOpen = false;
    public string $serverName = '';

    public function rules(): array
    {
        return [
            // TODO: The error message here comes out cryptic because this rule is intended for selects. Fix.
            'serverName' => Rules::string(1, 255)->in([$this->server->name])->required(),
        ];
    }

    public function openConfirmationModal(): void
    {
        $this->resetErrorBag();

        $this->dispatchBrowserEvent('confirmation-modal-opened');

        $this->confirmationModalOpen = true;
    }

    /** Delete the server. */
    public function delete(DeleteServerAction $delete): void
    {
        $this->authorize('delete', [$this->server]);

        $this->validate();

        $delete->execute($this->server);

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('servers.delete-server-form');
    }
}
