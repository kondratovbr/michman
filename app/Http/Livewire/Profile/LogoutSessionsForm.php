<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Validation\Rules;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection as BasicCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\Component;

/**
 * @property-read BasicCollection $sessions
 */
class LogoutSessionsForm extends Component
{
    use AuthorizesRequests;

    /** @var bool Indicates if a confirmation modal should currently be opened. */
    public bool $modalOpened = false;
    /** @var string Currently typed user's password. */
    public string $password = '';

    protected function rules(): array
    {
        return [
            'password' => Rules::currentUserPassword()->required(),
        ];
    }

    /** Open modal confirmation dialog. */
    public function openModal(): void
    {
        // We have to reset validation errors, otherwise errors
        // are still displayed when the modal is closed and then opened again.
        $this->resetErrorBag();
        $this->password = '';
        // We're using this event for focusing the password input.
        // We need a browser event here specifically,
        // because it will be caught by Alpine.
        $this->dispatchBrowserEvent('confirming-logout-sessions');
        $this->modalOpened = true;
    }

    /** Invalidate all browser sessions of the current user, beside the current one. */
    public function logoutOtherSessions(StatefulGuard $guard): void
    {
        $this->validate();

        $this->authorize('logoutOtherSessions', [Auth::user()]);

        DB::beginTransaction();

        /*
         * Here's a decent explanation of how it actually happens:
         * https://laracasts.com/series/whats-new-in-laravel-5-6/episodes/7
         * Also, see Illuminate\Session\Middleware\AuthenticateSession.
         * Btw, this middleware should be enabled for this to work.
         */
        $guard->logoutOtherDevices($this->password);

        /*
         * We have to manually delete other session records,
         * because they're invalidated by changing the user's password hash,
         * so sessions records will only be automatically cleaned
         * after they expire on their own.
         */
        $this->deleteOtherSessionRecords();

        DB::commit();

        // Close the modal.
        $this->modalOpened = false;

        // Emit a component event that will trigger the success message.
        $this->emit('loggedOut');
    }

    /** Delete the other browser session records from storage. */
    protected function deleteOtherSessionRecords(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    /** Get a list of auth'ed user's currently active sessions. */
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

    /** Create a new agent instance from the given session. */
    protected function createAgent($session): Agent
    {
        $agent = new Agent;
        $agent->setUserAgent($session->user_agent);
        return $agent;
    }

    public function render(): View
    {
        return view('profile.logout-sessions-form');
    }
}
