<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Actions\DatabaseUsers\StoreDatabaseUserAction;
use App\Broadcasting\ServerChannel;
use App\DataTransferObjects\DatabaseUserData;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseDeletedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class CreateDatabaseUserForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput,
        ListensForEchoes;

    public Server $server;

    public Collection $databases;

    public string|null $name = null;
    public string|null $password = null;
    /** @var bool[] Database key => (bool) true - databases to grant access to for the new user. */
    public array $grantedDatabases = [];

    /** @var string[] */
    protected $listeners = [
        'database-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            [
                DatabaseCreatedEvent::class,
                DatabaseDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    protected function prepareForValidation($attributes): array
    {
        $attributes['grantedDatabases'] = Arr::keys(Arr::filter($attributes['grantedDatabases']));

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'name' => Rules::alphaNumDashString(1, 255)->required(),
            'password' => Rules::alphaNumDashString(8, 255)->required(),
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
    public function store(StoreDatabaseUserAction $storeDatabaseUser): void {
        $validated = $this->validate();

        $this->authorize('create', [DatabaseUser::class, $this->server]);

        $storeDatabaseUser->execute(new DatabaseUserData(
            name: $validated['name'],
            password: $validated['password'],
        ),
            $this->server,
            Database::query()->findMany($validated['grantedDatabases']),
        );

        $this->reset(
            'name',
            'password',
            'grantedDatabases',
        );

        $this->emit('database-user-stored');
        $this->emit('database-updated');
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
