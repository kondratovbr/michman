<?php declare(strict_types=1);

namespace App\Http\Livewire\Daemons;

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
        'startSeconds' => 1,
    ];

    protected function stateRules(): array
    {
        return [
            'command' => Rules::string()->required(),
            'username' => Rules::alphaNumDashString(1, 255)->required(),
            'directory' => Rules::unixPath()->nullable(),
            'processes' => Rules::integer(1, 255)->required(),
            'startSeconds' => Rules::integer(1)->required(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', [Daemon::class, $this->server]);
    }

    /**
     * Store the newly configured daemon.
     */
    public function store(): void
    {
        $state = $this->validate();

        $this->authorize('create', [Daemon::class, $this->server]);

        //
    }

    public function render(): View
    {
        return view('daemons.create-daemon-form');
    }
}
