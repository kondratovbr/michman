<?php declare(strict_types=1);

namespace App\Http\Livewire\Daemons;

use App\Actions\Daemons\StoreDaemonAction;
use App\DataTransferObjects\DaemonData;
use App\Http\Livewire\Traits\HasState;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Daemon;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: Add an option to run not just a command, but a custom script.
 *       So, an optional (and hidden by default) field for a script, editing form in a modal (in the table), etc.
 *       Supervisor is often used with a script.
 */

// TODO: CRITICAL! Cover with tests!

class CreateDaemonForm extends LivewireComponent
{
    use AuthorizesRequests,
        HasState,
        TrimsInputBeforeValidation;

    public Server $server;

    public array $state = [
        'command' => '',
        'username' => 'michman',
        'directory' => null,
        'processes' => 1,
        'start_seconds' => 1,
    ];

    protected function prepareState(array $state): array
    {
        /*
         * Trim the '/' symbols from the end of the path since that's how we store paths to directories.
         */

        $dir = $state['directory'];

        if (! is_string($dir))
            return $state;

        $prefix = $dir[0] === '/' ? '/' : '';
        $dir = $prefix . trim($dir, '/');
        $state['directory'] = $dir;
        return $state;
    }

    protected function stateRules(): array
    {
        return [
            'command' => Rules::string()->required(),
            'username' => Rules::alphaNumDashString(1, 255)->required(),
            'directory' => Rules::unixPath()->nullable(),
            'processes' => Rules::integer(1, 255)->required(),
            'start_seconds' => Rules::integer(1)->required(),
        ];
    }

    protected function resetState(): void
    {
        $this->reset('state');
    }

    public function mount(): void
    {
        $this->authorize('create', [Daemon::class, $this->server]);
    }

    /**
     * Store the newly configured daemon.
     */
    public function store(StoreDaemonAction $action): void
    {
        $state = $this->validateState();

        $this->authorize('create', [Daemon::class, $this->server]);

        $action->execute(new DaemonData($state), $this->server);

        $this->resetState();

        $this->emit('daemon-stored');
    }

    public function render(): View
    {
        return view('daemons.create-daemon-form');
    }
}
