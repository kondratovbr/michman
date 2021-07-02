<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\DataTransferObjects\DatabaseData;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Database;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class CreateDatabaseForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput;

    public Server $server;

    public Collection $databaseUsers;

    public string|null $name = '';
    /** @var bool[] */
    public array $grantedUsers = [];

    /** @var string[] */
    protected $listeners = [
        // TODO: CRITICAL! Does this work? Test. The event is thrown from a different component on the same page.
        'database-user-stored' => '$refresh',
    ];

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => Rules::string(1, 255)->required(),
            'grantedUser' => Rules::array()->required(),
            'grantedUsers.*' => Rules::boolean(),
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
    public function store(StoreDatabaseAction $storeDatabase): void
    {
        dd($this->name, $this->databaseUsers);

        $validated = $this->validate();

        $this->authorize('create', [Database::class, $this->server]);

        $storeDatabase->execute(new DatabaseData(name: $validated['name']), $this->server);

        // TODO: CRITICAL! Don't forge to grant user-chosen database users access to the new database.

        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('databases.create-form');
    }
}
