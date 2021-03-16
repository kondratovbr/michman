<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as BasicCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\Component;

class LogoutSessionsForm extends Component
{
    /** @var bool Indicates if a confirmation modal should currently be opened. */
    public bool $modalOpened = false;
    /** @var string Currently typed user's password. */
    public string $password = '';

    /**
     * Open modal confirmation dialog.
     */
    public function openModal(): void
    {
        $this->password = '';
        $this->modalOpened = true;
    }

    public function logoutOtherSessions(): void
    {

    }

    /**
     * Get a list of auth'ed user's currently active sessions.
     */
    public function getSessionsProperty(): BasicCollection
    {
        // No way to get the list if we're using something but a DB to store sessions,
        // so just return an empty collection and handle it in the view.
        if (config('session.driver') !== 'database') {
            return collect();
        }

        // TODO: I would probably like to have DBOs or something in here instead of just an array of generic objects.
        return collect(
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'agent' => $this->createAgent($session),
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => CarbonImmutable::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     */
    protected function createAgent($session): Agent
    {
        $agent = new Agent;
        $agent->setUserAgent($session->user_agent);
        return $agent;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.logout-sessions-form');
    }
}
