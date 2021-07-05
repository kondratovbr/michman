<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Actions\DatabaseUsers\GrantDatabaseUserAccessToDatabase;
use App\Actions\DatabaseUsers\StoreDatabaseUserAction;
use App\DataTransferObjects\DatabaseUserData;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

class CreateDatabaseUserForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput;

    public Server $server;

    public Collection $databases;

    public string|null $name = null;
    public string|null $password = null;
    /** @var bool[] */
    public array $grantedDatabases = [];

    /** @var string[] */
    protected $listeners = [
        'database-stored' => '$refresh',
    ];

    public function prepareForValidation($attributes): array
    {
        $attributes['grantedDatabases'] = Arr::keys(Arr::filter($this->grantedDatabases));

        return $attributes;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            // TODO: CRITICAL! Make sure users cannot use reserved words like "information_schema".
            //       Those are different for each database as well.
            'name' => Rules::alphaNumDashString(1, 255)->required(),
            'password' => Rules::alphaNumDashString(1, 255)->required(),
            'grantedDatabases' => Rules::array(),
            'grantedDatabases.*' => Rules::integer()
                ->in($this->server->databases->pluck('id')->toArray()),
        ];
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('create', [DatabaseUser::class, $this->server]);
    }

    /**
     * Store a new database user.
     */
    public function store(
        StoreDatabaseUserAction $storeDatabaseUser,
        GrantDatabaseUserAccessToDatabase $grantAccess,
    ): void {
        $validated = $this->validate();

        $this->authorize('create', [DatabaseUser::class, $this->server]);

        // TODO: CRITICAL! Should these two actions be chained somehow? I cannot grant user privileges before the user is actually created and jobs may complete in random order.

        $databaseUser = $storeDatabaseUser->execute(new DatabaseUserData(
            name: $validated['name'],
            password: $validated['password'],
        ), $this->server);

        foreach ($validated['grantedDatabases'] as $grantedDatabaseKey) {
            /** @var Database $database */
            $database = Database::query()->findOrFail($grantedDatabaseKey);
            $grantAccess->execute($databaseUser, $database);
        }

        $this->reset(
            'name',
            'password',
            'grantedDatabases',
        );

        $this->emit('database-user-stored');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();

        return view('database-users.create-form');
    }
}
