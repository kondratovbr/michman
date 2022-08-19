<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use App\Facades\Auth;
use App\Models\Provider;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

// TODO: VERY IMPORTANT! Implement provider deletion/unlinking after refactoring DigitalOcean to OAuth.
//       NOTE: I think I should keep the old manual token way of connection DO as well - business accounts may want it.

class ProvidersIndexTable extends Component
{
    use AuthorizesRequests;

    /** @var string[] */
    protected $listeners = [
        'provider-stored' => '$refresh',
    ];

    public function render(): View
    {
        $this->authorize('indexUser', [Provider::class, user()]);

        return view('providers.index-table', [
            'providers' => user()->providers()->oldest()->get(),
        ]);
    }
}
