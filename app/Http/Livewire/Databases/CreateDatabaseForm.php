<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\Actions\DatabaseUsers\GrantDatabaseUserAccessToDatabase;
use App\DataTransferObjects\DatabaseData;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

class CreateDatabaseForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput;

    public Server $server;

    public Collection $databaseUsers;

    public string|null $name = null;
    /** @var bool[] */
    public array $grantedUsers = [];

    /** @var string[] */
    protected $listeners = [
        'database-user-stored' => '$refresh',
    ];

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            // TODO: CRITICAL! Make sure users cannot use reserved words like "information_schema".
            //       Those are different for each database as well.
            'name' => Rules::alphaNumDashString(1, 255)->required(),
            'grantedUsers' => Rules::array(),
            'grantedUsers.*' => Rules::integer()
                ->in($this->server->databaseUsers->pluck('id')->toArray()),
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
    public function store(
        StoreDatabaseAction $storeDatabase,
        GrantDatabaseUserAccessToDatabase $grantAccess,
    ): void {
        $grantedUsers = Arr::keys(Arr::filter($this->grantedUsers));

        $validated = Validator::make(
            [
                'name' => $this->name,
                'grantedUsers' => $grantedUsers,
            ],
            $this->rules(),
        )->validate();

        $this->authorize('create', [Database::class, $this->server]);

        DB::beginTransaction();

        // TODO: CRITICAL! Should these two actions be chained somehow? I cannot grant user privileges before the DB is actually created and jobs may complete in random order.

        $database = $storeDatabase->execute(new DatabaseData(
            name: $validated['name'],
        ), $this->server);

        foreach ($validated['grantedUsers'] as $grantedUserKey) {
            /** @var DatabaseUser $databaseUser */
            $databaseUser = DatabaseUser::query()->findOrFail($grantedUserKey);
            $grantAccess->execute($databaseUser, $database);
        }

        DB::commit();

        $this->reset(
            'name',
            'grantedUsers',
        );

        $this->emit('database-stored');
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
