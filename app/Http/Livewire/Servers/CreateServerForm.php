<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateServerForm extends Component
{
    public string $provider = '';

    /**
     * Provider name to form component mapping.
     */
    private array $formComponents = [
        'digital_ocean_v2' => 'servers.digital-ocean-form',
    ];

    /**
     * Get the name of a server creation form component for a chosen provider.
     */
    public function getFormComponentProperty(): ?string
    {
        // User just loaded this component and haven't chosen a provider yet.
        if ($this->provider === '')
            return null;

        // Shouldn't happen, so we gracefully fail but log an error and don't just throw the user to an error page.
        if (! isset($this->formComponents[$this->provider])) {
            Log::error('Tried to render a server creation form for an undeclared provider.');
            return null;
        }

        return $this->formComponents[$this->provider];
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.create-form');
    }
}
