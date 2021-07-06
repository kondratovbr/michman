<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Actions\Databases\StoreDatabaseAction;
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
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests!

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
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('index', [Database::class, $this->server]);
    }

    protected function prepareForValidation($attributes): array
    {
        $attributes['grantedUsers'] = Arr::keys(Arr::filter($attributes['grantedUsers']));

        return $attributes;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            /*
             * TODO: CRITICAL! Make sure users cannot use reserved words like "information_schema".
             *       Those are different for each database as well.
             *       Here's an example for MySQL 8.0:
             *       https://dev.mysql.com/doc/refman/8.0/en/keywords.html
             *       Make sure to validate database user names in the same manner as well.
             */
            'name' => Rules::alphaNumDashString(1, 255)->required(),
            'grantedUsers' => Rules::array(),
            'grantedUsers.*' => Rules::integer()
                ->in($this->server->databaseUsers->pluck(DatabaseUser::keyName())->toArray()),
        ];
    }

    /**
     * Store a new database.
     */
    public function store(StoreDatabaseAction $storeDatabase): void {
        $validated = $this->validate();

        $this->authorize('create', [Database::class, $this->server]);

        // TODO: CRITICAL! CONTINUE! Finish and test this. Don't forget to chain the jobs inside the action. And don't forget to do the same for the DatabaseUser creation part.
        $storeDatabase->execute(
            new DatabaseData(
                name: $validated['name'],
            ),
            $this->server,
            DatabaseUser::query()->findMany($validated['grantedUsers']),
        );

        $this->reset(
            'name',
            'grantedUsers',
        );

        $this->emit('database-stored');
        $this->emit('database-user-updated');
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
