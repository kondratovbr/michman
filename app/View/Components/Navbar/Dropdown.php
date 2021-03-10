<?php declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public function __construct()
    {
        //
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.navbar.dropdown');
    }
}
