<?php declare(strict_types=1);

namespace App\Http\Livewire\UserSshKeys;

use App\Facades\Auth;
use App\Models\UserSshKey;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! CONTINUE. Implement adding these keys to new servers, implement adding them to existing servers and implement two removal options - just remove from Michman or remove from all servers as well.

class UserSshKeysIndexTable extends LivewireComponent
{
    use AuthorizesRequests;

    public Collection $keys;

    protected $listeners = [
        'user-ssh-key-stored' => '$refresh',
    ];

    public function mount(): void
    {
        $this->authorize('index', [UserSshKey::class, Auth::user()]);
    }

    public function render(): View
    {
        $this->keys = Auth::user()->userSshKeys()->oldest()->get();

        return view('user-ssh-keys.user-ssh-keys-index-table');
    }
}
