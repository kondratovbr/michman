<?php declare(strict_types=1);

namespace App\View\Components;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VcsPage extends Component
{
    public function __construct(
        public User $user,
    ) {}

    public function render(): View
    {
        return view('vcs.page');
    }
}
