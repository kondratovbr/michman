<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Database;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class CreateDatabaseForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput;

    public Server $server;

    public string|null $name = '';

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => Rules::string(1, 255)->required(),
        ];
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('index', [Database::class, $this->server]);
    }

    /**
     * Store a new database.
     */
    public function store(): void
    {
        $validated = $this->validate();

        $this->authorize('create', [Database::class, $this->server]);

        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('databases.create-form');
    }
}
