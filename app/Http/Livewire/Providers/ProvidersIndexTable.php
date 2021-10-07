<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use App\Facades\Auth;
use App\Models\Provider;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ProvidersIndexTable extends Component
{
    use AuthorizesRequests;

    /** @var string[] */
    protected $listeners = [
        'provider-stored' => '$refresh',
    ];

    public function render(): View
    {
        $this->authorize('indexUser', [Provider::class, Auth::user()]);

        return view('providers.index-table', [
            'providers' => Auth::user()->providers()->oldest()->get(),
        ]);
    }
}
